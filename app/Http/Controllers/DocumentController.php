<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Setting;
use App\Document;
use App\User;
use App\DocumentCategory;
use Storage;
use App\Email;
use App\Vendor;
use App\ApiKey;
use App\Contact;
use App\Mail\DocumentEmail;
use App\DocumentRemark;
use App\DocumentHistory;
use Mail;

class DocumentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

            $documents = Document::where('status',1)->latest()->paginate(Setting::get('pagination'));
            $users = User::select(['id', 'name', 'email', 'agent_role'])->get();
            $category = DocumentCategory::select('id', 'name')->get();
            $api_keys = ApiKey::select('number')->get();
            return view('documents.index', [
                'documents' => $documents,
                'users' => $users,
                'category' => $category,
                'api_keys' => $api_keys,
            ]);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'user_id' => 'required|numeric',
            'name' => 'required|string|max:255',
            'file' => 'required',
            'category_id' => 'required',
            'version' => 'required'
        ]);

        $data = $request->except(['_token', 'file']);
       // dd($data);
        foreach ($request->file('file') as $file) {
            $data[ 'filename' ] = $file->getClientOriginalName();

            $file->storeAs("documents", $data[ 'filename' ], 'files');

            Document::create($data);
        }

        return redirect()->route('document.index')->withSuccess('You have successfully uploaded document(s)!');
    }

    public function download($id)
    {
        $document = Document::find($id);

        return Storage::disk('files')->download('documents/' . $document->filename);
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $document = Document::findorfail($id);
        $document->user_id = $request->user_id;
        $document->name = $request->name;
        $document->category_id = $request->category_id;
        $document->status = 1;
        $document->update();

     return redirect()->route('document.index')->withSuccess('You have successfully updated document!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $document = Document::find($id);

        Storage::disk('files')->delete("documents/$document->filename");

        $document->delete();

        return redirect()->route('document.index')->withSuccess('You have successfully deleted document');
    }


    public function sendEmailBulk(Request $request)
    {

        $this->validate($request, [
            'subject' => 'required|min:3|max:255',
            'message' => 'required',
            'cc.*' => 'nullable|email',
            'bcc.*' => 'nullable|email'
        ]);


        $file_paths = [];

        if ($request->hasFile('file')) {
            foreach ($request->file('file') as $file) {
                $filename = $file->getClientOriginalName();

                $file->storeAs("documents", $filename, 'files');

                $file_paths[] = "documents/$filename";
            }
        }

        $document = Document::findOrFail($request->document_id);

        if ($document) {
            $file_paths[] = "documents/$document->filename";
        }

        $cc = $bcc = [];
        if ($request->has('cc')) {
            $cc = array_values(array_filter($request->cc));
        }
        if ($request->has('bcc')) {
            $bcc = array_values(array_filter($request->bcc));
        }

        if ($request->user_type == 1) {
            foreach ($request->users as $key) {
                $user = User::findOrFail($key);

                $mail = Mail::to($user->email);

                if ($cc) {
                    $mail->cc($cc);
                }
                if ($bcc) {
                    $mail->bcc($bcc);
                }

                $mail->send(new DocumentEmail($request->subject, $request->message, $file_paths));

                $params = [
                    'model_id' => $user->id,
                    'model_type' => User::class,
                    'from' => 'documents@amourint.com',
                    'seen' => 1,
                    'to' => $user->email,
                    'subject' => $request->subject,
                    'message' => $request->message,
                    'template' => 'customer-simple',
                    'additional_data' => json_encode(['attachment' => $file_paths]),
                    'cc' => $cc ? : null,
                    'bcc' => $bcc ? : null,
                ];

                Email::create($params);
            }
        } elseif ($request->user_type == 2) {
            foreach ($request->users as $key) {
                $vendor = Vendor::findOrFail($key);

                $mail = Mail::to($vendor->email);

                if ($cc) {
                    $mail->cc($cc);
                }
                if ($bcc) {
                    $mail->bcc($bcc);
                }

                $mail->send(new DocumentEmail($request->subject, $request->message, $file_paths));

                $params = [
                    'model_id' => $vendor->id,
                    'model_type' => Vendor::class,
                    'from' => 'documents@amourint.com',
                    'seen' => 1,
                    'to' => $vendor->email,
                    'subject' => $request->subject,
                    'message' => $request->message,
                    'template' => 'customer-simple',
                    'additional_data' => json_encode(['attachment' => $file_paths]),
                    'cc' => $cc ? : null,
                    'bcc' => $bcc ? : null,
                ];

                Email::create($params);
            }

        } elseif ($request->user_type == 3) {
            foreach ($request->users as $key) {
                $contact = Contact::findOrFail($key);

                $mail = Mail::to($contact->email);

                if ($cc) {
                    $mail->cc($cc);
                }
                if ($bcc) {
                    $mail->bcc($bcc);
                }

                $mail->send(new DocumentEmail($request->subject, $request->message, $file_paths));

                $params = [
                    'model_id' => $contact->id,
                    'model_type' => Contact::class,
                    'from' => 'documents@amourint.com',
                    'seen' => 1,
                    'to' => $contact->email,
                    'subject' => $request->subject,
                    'message' => $request->message,
                    'template' => 'customer-simple',
                    'additional_data' => json_encode(['attachment' => $file_paths]),
                    'cc' => $cc ? : null,
                    'bcc' => $bcc ? : null,
                ];

                Email::create($params);
            }
        }
        return redirect()->route('document.index')->withSuccess('You have successfully sent emails in bulk!');
    }

    public function getTaskRemark(Request $request)
    {
        $id = $request->input('id');

        $remark = DocumentRemark::where('document_id', $id)->get();

        return response()->json($remark, 200);
    }

    public function addRemark(Request $request)
    {
        $remark = $request->input('remark');
        $id = $request->input('id');
        $created_at = date('Y-m-d H:i:s');
        $update_at = date('Y-m-d H:i:s');
        if ($request->module_type == "document") {
            $remark_entry = DocumentRemark::create([
                'document_id' => $id,
                'remark' => $remark,
                'module_type' => $request->module_type,
                'user_name' => $request->user_name ? $request->user_name : Auth::user()->name
            ]);
        }

        return response()->json(['remark' => $remark], 200);

    }

    public function uploadDocument(Request $request)
    {

        $document = Document::findOrFail($request->document_id);

        //Create Document History
        $document_history = new DocumentHistory();
        $document_history->document_id = $document->id;
        $document_history->category_id = $document->category_id;
        $document_history->user_id = $document->user_id;
        $document_history->name = $document->name;
        $document_history->filename = $document->filename;
        $document_history->version = $document->version;
        $document_history->save();

        //Update the version and files name
        $document->version = ($document->version + 1);
        $file = $request->file('files');
        $document->filename = $file->getClientOriginalName();
        $file->storeAs("documents", $document->filename, 'files');
        $document->save();

        return redirect()->route('document.index')->withSuccess('You have successfully uploaded document(s)!');

    }

    public function getDataByUserType(Request $request)
    {

        if ($request->selected == 1) {

            $user = User::select('id', 'name')->get();

            $output = '';

            foreach ($user as $users) {
                $output .= '<option value="' . $users[ "id" ] . '">' . $users[ "name" ] . '</option>';
            }
            echo $output;

        } elseif ($request->selected == 2) {

            $vendors = Vendor::select('id', 'name')->get();

            $output = '';

            foreach ($vendors as $vendor) {
                $output .= '<option value="' . $vendor[ "id" ] . '">' . $vendor[ "name" ] . '</option>';
            }
            echo $output;

        } elseif ($request->selected == 3) {

            $contact = Contact::select('id', 'name')->get();

            $output = '';

            foreach ($contact as $contacts) {
                $output .= '<option value="' . $contacts[ "id" ] . '">' . $contacts[ "name" ] . '</option>';
            }
            echo $output;

        } else {

            $output .= '<option value="0">Not Founf</option>';

            echo $output;

        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function email()
    {
        $documents = Document::where('status',0)->latest()->paginate(Setting::get('pagination'));
        $users = User::select(['id', 'name', 'email', 'agent_role'])->get();
        $category = DocumentCategory::select('id', 'name')->get();
        $api_keys = ApiKey::select('number')->get();
        return view('documents.email', [
            'documents' => $documents,
            'users' => $users,
            'category' => $category,
            'api_keys' => $api_keys,
        ]);

    }
}