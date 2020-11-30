<?php

namespace App\Http\Controllers;


\InstagramAPI\Instagram::$allowDangerousWebUsageAtMyOwnRisk = true;

use App\Account;
use App\HashTag;
use App\InstagramPosts;
use App\Post;
use App\InstagramPostsComments;
use App\Setting;
use Illuminate\Http\Request;
use InstagramAPI\Instagram;
use Plank\Mediable\MediaUploaderFacade as MediaUploader;
use File;
use App\CommentsStats;
use App\InstagramCommentQueue;
use App\ScrapInfluencer;
use Carbon\Carbon;
use App\InstagramUsersList;
use App\Library\Instagram\PublishPost;
use Plank\Mediable\Media;
use App\StoreSocialContent;

class InstagramPostsController extends Controller
{
    public function index(Request $request)
    {
        // Load posts
        if($request->hashtag){
            $posts = $this->_getFilteredInstagramPosts($request);
        }else{
            $posts = InstagramPosts::orderBy('id','desc');
        }
        // Paginate
        $posts = $posts->paginate(Setting::get('pagination'));
        // Return view
        return view('social-media.instagram-posts.index', compact('posts'));
    }


    public function post(Request $request)
    {
        //$accounts = \App\Account::where('platform','instagram')->whereNotNull('proxy')->where('status',1)->get();
        $accounts = \App\Account::where('platform','instagram')->where('status',1)->get();

        //$posts = Post::where('status', 1)->get();
        
        $query = Post::query();
        
        if($request->acc){
            $query = $query->where('id', $request->acc);
        }
        if($request->comm){
            $query = $query->where('comment', 'LIKE','%'.$request->comm.'%');
        }
        if($request->tags){
            $query = $query->where('hashtags', 'LIKE','%'.$request->tags.'%');
        }
        if($request->loc){
            $query = $query->where('location', 'LIKE','%'.$request->loc.'%');
        }
        $posts = $query->orderBy('id', 'asc')->paginate(25)->appends(request()->except(['page']));


        $used_space = 0;
        $storage_limit = 0;
        $contents = StoreSocialContent::query();
        $contents = $contents->get();
        $records = [];
        foreach($contents as $site) {
            if ($site) {
                    if ($site->hasMedia(config('constants.media_tags'))) {
                        foreach ($site->getMedia(config('constants.media_tags')) as $media) {                                  
                            $records[] = [
                                "id"        => $media->id,
                                'extension' => strtolower($media->extension), 
                                'file_name' => $media->filename, 
                                'mime_type' => $media->mime_type, 
                                'size' => $media->size , 
                                'thumb' => $media->getUrl() , 
                                'original' => $media->getUrl() 
                            ];
                        }
                    }
            }
        }
        return view('instagram.post.create' , compact('accounts','records','used_space','storage_limit', 'posts'))->with('i', ($request->input('page', 1) - 1) * 5);;   
    }

    public function createPost(Request $request){
        
        //resizing media 
        
        $all = $request->all();

        //dd($request->media);
        if($request->media)
        {
            foreach ($request->media as $media) {
               
                $mediaFile = Media::where('id',$media)->first();
                $image = self::resize_image_crop($mediaFile,640,640);
            }
        }
        

        $post = new Post();
        $post->account_id = $request->account;
        $post->type       = $request->type;
        $post->caption    = $request->caption;
        $ig         = [
            'media'    => $request->media,
            'location' => '',
        ];
        $post->ig       = json_encode($ig);
        $post->location = $request->location;
        $post->hashtags = $request->hashtags;
        $post->save();
        return redirect()->route('post.index')
                ->with('success', __('Your post has been saved'));

        /*if (new PublishPost($post)) {
            return redirect()->route('post.index')
                ->with('success', __('Your post has been published'));
        } else {
            return redirect()->route('post.index')
                ->with('error', __('Post failed to published'));
        }*/

    }


    public function publishPost(Request $request, $id){
       
        $post = Post::find($id);
        $media = json_decode($post->ig,true);
        $ig         = [
            'media'    => $post->media,
            'location' => '',
        ];
        $post->ig = $ig;
        if (new PublishPost($post)) {
            return redirect()->route('post.index')
                ->with('success', __('Your post has been published'));
        } else {
            return redirect()->route('post.index')
                ->with('error', __('Post failed to published'));
        }

    }



    public function grid(Request $request)
    {
        // Load posts
        $posts = $this->_getFilteredInstagramPosts($request);

        // Paginate
        $posts = $posts->paginate(Setting::get('pagination'));

        // For ajax
        if ($request->ajax()) {
            return response()->json([
                'tbody' => view('social-media.instagram-posts.json_grid', compact('posts'))->render(),
                'links' => (string)$posts->appends($request->all())->render()
            ], 200);
        }

        // Return view
        return view('social-media.instagram-posts.grid', compact('posts', 'request'));
    }

    private function _getFilteredInstagramPosts(Request $request) {
        // Base query
        $instagramPosts = InstagramPosts::orderBy('posted_at', 'DESC')
            ->join('hash_tags', 'instagram_posts.hashtag_id', '=', 'hash_tags.id')
            ->select(['instagram_posts.*','hash_tags.hashtag']);

        //Ignore google search result from DB
        $instagramPosts->where('source', '!=', 'google');
        
        // Apply hashtag filter
        if (!empty($request->hashtag)) {
            $instagramPosts->where('hash_tags.hashtag', str_replace('#', '', $request->hashtag));
        }

        // Apply author filter
        if (!empty($request->author)) {
            $instagramPosts->where('username', 'LIKE', '%' . $request->author . '%');
        }

        // Apply author filter
        if (!empty($request->post)) {
            $instagramPosts->where('caption', 'LIKE', '%' . $request->post . '%');
        }

        // Return instagram posts
        return $instagramPosts;
    }

    public function apiPost(Request $request)
    {
        // Get raw body
        $file = $request->file('file');

        $f = File::get($file);

        $payLoad = json_decode($f);

        // NULL? No valid JSON
        if ($payLoad == null) {
            return response()->json([
                'error' => 'Invalid json'
            ], 400);
        }

        // Process input
        if (is_array($payLoad) && count($payLoad) > 0) {
            $payLoad = json_decode(json_encode($payLoad), true);

            // Loop over posts
            foreach ($payLoad as $postJson) {

                if(isset($postJson['Followers'])){
                    
                    $inf = ScrapInfluencer::where('name',$postJson['Owner'])->first();
                    if($inf == null){
                        $influencer = new ScrapInfluencer;
                        $influencer->name = $postJson['Owner'];
                        $influencer->url = $postJson['URL'];
                        $influencer->followers = $postJson['Followers'];
                        $influencer->following = $postJson['Following'];
                        $influencer->posts = $postJson['Posts'];
                        $influencer->description = $postJson['Bio'];
                        if(isset($postJson['keyword'])){
                            $influencer->keyword = $postJson['keyword'];
                        }
                        $influencer->save();
                    }
                }else{
                        // Set tag
                    $tag = $postJson[ 'Tag used to search' ];

                    // Get hashtag ID
                    $hashtag = HashTag::firstOrCreate(['hashtag' => $tag]);
                    $hashtag->is_processed = 1;
                    $hashtag->save();

                    // Retrieve instagram post or initiate new
                    $instagramPost = InstagramPosts::firstOrNew(['location' => $postJson[ 'URL' ]]);
                    $instagramPost->hashtag_id = $hashtag->id;
                    $instagramPost->username = $postJson[ 'Owner' ];
                    $instagramPost->caption = $postJson[ 'Original Post' ];
                    $instagramPost->posted_at = date('Y-m-d H:i:s', strtotime($postJson[ 'Time of Post' ]));
                    $instagramPost->media_type = !empty($postJson[ 'Image' ]) ? 'image' : 'other';
                    $instagramPost->media_url = !empty($postJson[ 'Image' ]) ? $postJson[ 'Image' ] : $postJson[ 'URL' ];
                    $instagramPost->source = 'instagram';
                    $instagramPost->save();

                    // Store media
                    if (!empty($postJson[ 'Image' ])) {
                        if (!$instagramPost->hasMedia('instagram-post')) {
                            $media = MediaUploader::fromSource($postJson[ 'Image' ])
                                ->toDisk('uploads')
                                ->toDirectory('social-media/instagram-posts/' . floor($instagramPost->id / 1000))
                                ->useFilename($instagramPost->id)
                                ->beforeSave(function (\Plank\Mediable\Media $model, $source) {
                                    $model->setAttribute('extension', 'jpg');
                                })
                                ->upload();
                            $instagramPost->attachMedia($media, 'instagram-post');
                        }
                    }

                    // Comments
                    if (isset($postJson[ 'Comments' ]) && is_array($postJson[ 'Comments' ])) {
                        // Loop over comments
                        foreach ($postJson[ 'Comments' ] as $comment) {
                            // Check if there really is a comment
                            if (isset($comment[ 'Comments' ][ 0 ])) {
                                // Set hash
                                $commentHash = md5($comment[ 'Owner' ] . $comment[ 'Comments' ][ 0 ] . $comment[ 'Time' ]);

                                $instagramPostsComment = InstagramPostsComments::firstOrNew(['comment_id' => $commentHash]);
                                $instagramPostsComment->instagram_post_id = $instagramPost->id;
                                $instagramPostsComment->comment_id = $commentHash;
                                $instagramPostsComment->username = $comment[ 'Owner' ];
                                $instagramPostsComment->comment = $comment[ 'Comments' ][ 0 ];
                                $instagramPostsComment->posted_at = date('Y-m-d H:i:s', strtotime($comment[ 'Time' ]));
                                $instagramPostsComment->save();
                            }
                        }
                    } 
                }
                
            }
        }

        // Return
        return response()->json([
            'ok'
        ], 200);
    }


    public function sendAccount($token)
    {
      if($token != 'sdcsds'){
        return response()->json(['message' => 'Invalid Token'], 400);
      }
      $account = Account::where('platform','instagram')->where('comment_pending',1)->first();

     return response()->json(['username' => $account->last_name , 'password' => $account->password], 200);
    }

    public function getComments($username)
    {
        $account = Account::where('last_name',$username)->first();

        if($account == null && $account == ''){
             return response()->json(['result' =>  false,'message' => 'Account Not Found'], 400);
        }

        $comments = InstagramCommentQueue::select('id','post_id','message')->where('account_id',$account->id)->where('is_send',0)->take(20)->get();
        if(count($comments) != 0){
            return response()->json(['result' => true , 'comments' => $comments],200); 
        }else{
            return response()->json(['result' =>  false, 'message' => 'No messages'],200); 
        }
               

    }

    public function commentSent(Request $request)
    {
        $id = $request->id;
        $comment = InstagramCommentQueue::find($id);
        $comment->is_send = 1;
        $comment->save();

    }    

    public function getHashtagList()
    {
        $hastags = HashTag::select('id','hashtag')->where('is_processed',0)->first();

        if(!$hastags){
            $hastags = HashTag::select('id','hashtag')->where('is_processed',2)->first();
        }
        
        if(!$hastags){
            return response()->json(['hastag' => ''],200);
        }

        return response()->json(['hastag' => $hastags ],200);

    }

    public function saveFromLocal(Request $request)
    {
        // Get raw JSON
        $receivedJson = json_decode($request->getContent());
        
        //Saving post details 
        if(isset($receivedJson->post)){
            
            $checkIfExist = InstagramPosts::where('post_id', $receivedJson->post->post_id)->first();

            if(empty($checkIfExist)){
                $media             = new InstagramPosts();
                $media->post_id    = $receivedJson->post->post_id;
                $media->caption    = $receivedJson->post->caption;
                $media->user_id    = $receivedJson->post->user_id;
                $media->username   = $receivedJson->post->username;
                $media->media_type = $receivedJson->post->media_type;
                $media->code       = $receivedJson->post->code;
                $media->location   = $receivedJson->post->location;
                $media->hashtag_id = $receivedJson->post->hashtag_id;
                $media->likes = $receivedJson->post->likes;
                $media->comments_count = $receivedJson->post->comments_count;
                $media->media_url = $receivedJson->post->media_url;
                $media->posted_at = $receivedJson->post->posted_at;
                $media->save();

            if($media){
                if(isset($receivedJson->comments)){
                    $comments = $receivedJson->comments;
                        foreach ($comments as $comment) {

                            $commentEntry = InstagramPostsComments::where('comment_id', $comment->comment_id)->where('user_id', $comment->user_id)->first();

                            if (!$commentEntry) {
                                $commentEntry = new InstagramPostsComments();
                            }

                            $commentEntry = new InstagramPostsComments();
                            $commentEntry->user_id = $comment->user_id;
                            $commentEntry->name = $comment->name;
                            $commentEntry->username = $comment->username;
                            $commentEntry->instagram_post_id = $comment->instagram_post_id;
                            $commentEntry->comment_id = $comment->comment_id;
                            $commentEntry->comment = $comment->comment;
                            $commentEntry->profile_pic_url = $comment->profile_pic_url;
                            $commentEntry->posted_at = $comment->posted_at;
                            $commentEntry->save();
                    }        
                        }
                }

            if(isset($receivedJson->userdetials)){    
                $detials = $receivedJson->userdetials;
                $userList = InstagramUsersList::where('user_id',$detials->user_id)->first();
                if(empty($userList)){
                    $user = new InstagramUsersList;
                    $user->username = $detials->username;
                    $user->user_id = $detials->user_id;
                    $user->image_url = $detials->image_url;
                    $user->bio = $detials->bio;
                    $user->rating = 0;
                    $user->location_id = 0;
                    $user->because_of = $detials->because_of;
                    $user->posts = $detials->posts;
                    $user->followers = $detials->followers;
                    $user->following = $detials->following;
                    $user->location = $detials->location;
                    $user->save();
                }else{
                    if($userList->posts == ''){
                        $userList->posts = $detials->posts;
                        $userList->followers = $detials->followers;
                        $userList->following = $detials->following;
                        $userList->location = $detials->location;
                        $userList->save();
                    }
                }        
            } 


          }     

            
        }
    }

    public function viewPost(Request $request)
    {
        $accounts = Account::where('platform','instagram')->whereNotNull('proxy')->get();

        $data = Post::whereNotNull('id')->paginate(10);
        
        return view('instagram.post.index', compact(
            'accounts',
            'data'
        ));
    }


    public function users(Request $request)
    {
        $users = \App\InstagramUsersList::whereNotNull('username')->where('is_manual',1)->orderBy('id','desc')->paginate(25);
        return view('instagram.users',compact('users'));
    }


    public function getUserForLocal()
    {
        $users = \App\InstagramUsersList::select('id','user_id')->whereNotNull('username')->where('is_manual',1)->where('is_processed',0)->orderBy('id','desc')->first();
        return json_encode($users);
        
    }

    public function userPost($id)
    {
        dd($id);
    }

    public function resizeToRatio()
    {
        
    }

    public  function resize_image_crop($image,$width,$height) {
        
        $newImage = $image;
        $type = $image->mime_type;
        
        if($type == 'image/jpeg'){
            $src_img = imagecreatefromjpeg($image->getAbsolutePath());    
        }elseif($type == 'image/png'){
            $src_img = imagecreatefrompng($image->getAbsolutePath());
        }elseif ($type == 'image/gif') {
            $src_img = imagecreatefromgif($image->getAbsolutePath());
        }
        
        $image = $src_img;
        $w = imagesx($image); //current width
        
        $h = @imagesy($image); //current height
        
        if ((!$w) || (!$h)) { $GLOBALS['errors'][] = 'Image could not be resized because it was not a valid image.'; return false; }
        if (($w == $width) && ($h == $height)) { return $image; } //no resizing needed

        //try max width first...
        $ratio = $width / $w;
        $new_w = $width;
        $new_h = $h * $ratio;

        //if that created an image smaller than what we wanted, try the other way
        if ($new_h < $height) {
            $ratio = $height / $h;
            $new_h = $height;
            $new_w = $w * $ratio;
        }

        $image2 = imagecreatetruecolor ($new_w, $new_h);
        imagecopyresampled($image2,$image, 0, 0, 0, 0, $new_w, $new_h, $w, $h);

        //check to see if cropping needs to happen

        $image3 = imagecreatetruecolor($width, $height);
        if ($new_h > $height) { //crop vertically
            $extra = $new_h - $height;
            $x = 0; //source x
            $y = round($extra / 2); //source y
            imagecopyresampled($image3,$image2, 0, 0, $x, $y, $width, $height, $width, $height);
        }
        else {
            $extra = $new_w - $width;
            $x = round($extra / 2); //source x
            $y = 0; //source y
            imagecopyresampled($image3,$image2, 0, 0, $x, $y, $width, $height, $width, $height);
        }
        imagedestroy($image2);
        imagejpeg($image3,$newImage->getAbsolutePath());
        return $image3;
     

    }

    public function hashtag(Request $request, $word)
    {

        if($word)
        {
            $response = $this->getHastagifyApiToken();

            if($response)
            {
                $json = $this->getHashTashSuggestions($response, $word);

                $arr = json_decode($json, true);

                $instaTags = [];
                if(isset($arr['code']) && $arr['code']=='404')
                {
                    //handle for error
                }else{
                    foreach ($arr as $tag) {
                       $instaTagData['name'] = $tag['name'];
                       $instaTagData['variants'] = [];
                       foreach ($tag['variants'] as $variant) {
                            $instaTagData['variants'][] = $variant[0];
                       }

                       $instaTagData['influencers'] = [];

                       foreach ($tag['top_influencers'] as $influencer) {
                            $instaTagData['influencers'][] = $influencer[0];
                       }

                       $instaTagData['popular'] = $tag['popularity'];

                       $instaTags[] = $instaTagData;
                    }
                }
                return response()->json(['response' => true , 'results' => $instaTags],200); 
            }
        }
    }


    public function getHastagifyApiToken()
    {
        
            $token = \Session()->get('hastagify');
            if($token){
                return $token;
            }else{
            
            $consumerKey = env('HASTAGIFY_CONSUMER_KEY');
            $consumerSecret = env('HASTAGIFY_CONSUMER_SECRET');
            
            $curl = curl_init();

            curl_setopt_array($curl, array(
              CURLOPT_URL => "https://api.hashtagify.me/oauth/token",
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => "",
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 30,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => "POST",
              CURLOPT_POSTFIELDS => "grant_type=client_credentials&client_id=".$consumerKey."&client_secret=".$consumerSecret,
              CURLOPT_HTTPHEADER => array(
                "cache-control: no-cache",
                "content-type: application/x-www-form-urlencoded"
              ),
            ));

            $response = curl_exec($curl);
            $err = curl_error($curl);

            curl_close($curl);

            if ($err) {
                return false;
            } else {
                $response = json_decode($response);
                \Session()->put('hastagify', $response->access_token);
                return $response->access_token;          
            } 
        }
    }


    public function getHashTashSuggestions($token, $word)
    {

        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => "https://api.jsonbin.io/b/5fbe49764f12502c21d85d06",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => array(
            "authorization: Bearer ".$token,
            "cache-control: no-cache"
          ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
          //echo "cURL Error #:" . $err;
        } else {
          return  $response;
        }
    }

    public function updateHashtagPost(Request $request)
    {
        $post_id = $request->get('post_id');
        $updateArr = [];


        if($request->get('account_id'))
        {
            $updateArr['account_id'] = $request->get('account_id');
        }
        if($request->get('comment'))
        {
            $updateArr['comment'] = $request->get('comment');
        }
        if($request->get('post_hashtags'))
        {
            $updateArr['hashtags'] = $request->get('post_hashtags');
        }
        if($request->get('type'))
        {
            $updateArr['type'] = $request->get('type');
        }
        Post::where('id', $post_id)->update($updateArr);
        echo json_encode(array("message"=>"Data Updated Succesfully"));
    }

}
