<!-- Purpose : Add class infinite-scroll - DEVTASK-4271 -->
<div class="infinite-scroll scrapper-python-image customer-count infinite-scroll-data customer-list-{{$website_id}} customer-{{$website_id}}" style="padding: 0px 10px;display: grid">
        @php
            $oldDate = null;
            $count   = 0;
           
            $device = ((isset($_REQUEST['device'])) ? $_REQUEST['device'] : '' );

            if($device == "desktop" || $device == ""){
                 $imageHeight = 'fit-content';
                 $imageWidth = 'infinite-scroll-images';
                 $imageDimensioClass ='image-diamention-rasio-desktop';
                 $width = 'fit-content';
            }
            else if($device == "tablet"){
                 $imageHeight = '800px';
                 $imageWidth = 'infinite-scroll-images-tablet';
                 $imageDimensioClass ='image-diamention-rasio-desktop';
                 $width = '100%';

            }
            else if($device == "mobile"){
                 $imageHeight = '600px';
                 $imageWidth = 'infinite-scroll-images-mobile';
                 $imageDimensioClass ='image-diamention-rasio-mobile';
                 $width = '100%';
            }
            
        @endphp

        <div class="row">
            <div class="col-md-12">
                <br>
                <h5 class="product-attach-date" style="margin: 5px 0px;"> Number Of Images:{{$images->total()}}</h5> 

                <hr style="margin: 5px 0px;">
            </div>
        </div>
        <div class="image-1"> 
        @if(!$images->isEmpty())
            @foreach($images as $image)
                    <?php $image = $image->toArray();?>
                    {{-- @foreach($imageM->scrapperImage->toArray() as $image) --}}

                    <?php
                        if ( date( 'Y-m-d' ,strtotime($image['created_at'])) !== $oldDate ) { 
                            $count = 0; 
                            $oldDate = date( 'Y-m-d' ,strtotime($image['created_at']));
                        ?>
                            <!-- <div class="row">
                                <div class="col-md-12">
                                    <br>
                                    <h5 class="product-attach-date" style="margin: 5px 0px;">{{$image['created_at']}} || Number Of Images:{{count($images)}}</h5> 

                                    <hr style="margin: 5px 0px;">
                                </div>
                            </div>  -->

                        <?php } ?>
                        
                    @if ($image['img_name'] )
                        @php
                        if($count == 6){
                            $count = 0;
                        }
                        
                        @endphp
                            {{-- START - Purpose : Comment Code - DEVTASK-4271 --}}
                            {{--  @if($count == 0)
                                <div class="row parent-row">
                            @endif --}}
                            {{-- END - DEVTASK-4271 --}}
                            <div class="{{ $imageWidth }}">               
                                @if ($image['coordinates'])
                                    @php 
                                        $x = 0;
                                        $coordinates = explode(',',$image['coordinates']);
                                        array_push($coordinates,$image['height']);
                                        $total_img_height = $image['height'];
                                    @endphp


                                    <div  style="position: relative;display: flex">
                                        <div class="image-diamention-rasio {{ $imageDimensioClass }}"  style="max-height: {{ $imageHeight }};">
                                            @foreach ($coordinates as $z)
                                            @php
                                                if($device == "mobile"){
                                                    $z = ceil((2372*$z)/$total_img_height);
                                                }
                                                if($device == "tablet"){
                                                    $z = ceil((5070*$z)/$total_img_height);
                                                }
                                            @endphp
                                            <td>
                                                <img data-coordinates="{{ $z}}" class="manage-product-image" src="{{ asset( 'scrappersImages/'.$image['img_name']) }}" style="object-position: 100% -{{ $x }}px;height:{{ $z - $x+20 }}px;width:{{ $width }}">
                                            </td>
                                            @php $x = $z; @endphp
                                        @endforeach
                                        </div>
                                        <button class="btn btn-secondarys add-remark-button" data-toggle="modal" data-target="#remark-area-list"><i class="fa fa-comments"></i></button>  
                                        <a class="btn btn-secondarys" href="{{$image['img_url']}}" target="_blank">Go to Url</a>  
                                        @if($image['url'] != "")
                                        <a class="btn btn-secondarys" href="{{env('APP_URL')}}/scrapper-python/image/url_list?flagUrl={{$image['id']}}" target="_blank">Go to Link</a>  
                                        @endif
                                        {{ \Carbon\Carbon::parse($image['created_at'])->format('d-m-y') }}
                                    </div>
                                @else
                                    <div class="col-md-12 col-xs-12 text-center product-list-card mb-4 p-0" style="position: relative;display: flex">
                                        <div class="product-list-card" style="position: relative;padding:0;margin-bottom:2px !important;max-height:{{ $imageHeight }};overflow-y:auto;overflow-x: hidden">

                                            <div data-interval="false" id="carousel_{{ $image['id'] }}" class="carousel slide" data-ride="carousel">
                                                <a href="#" data-toggle="tooltip" data-html="true" data-placement="top" >
                                                    <div class="carousel-inner maincarousel">
                                                        <div class="item" style="display: block;"> <a data-fancybox="gallery" href="{{ urldecode(asset( 'scrappersImages/'.$image['img_name']))}}" ><img src="{{ urldecode(asset( 'scrappersImages/'.$image['img_name']))}}" style="height: 100%; width: 100%; max-width:fit-content; display: block;margin-left: auto;margin-right: auto;"> </a> </div>
                                                    </div>
                                                </a>
                                            </div>

                                            <div class="row pl-4 pr-4" style="padding: 0px; margin-bottom: 8px;">
                            
                                            </div>
                                        </div>
                                        <button class="btn btn-secondarys add-remark-button" data-toggle="modal" data-target="#remark-area-list"><i class="fa fa-comments"></i></button>  
                                        <a class="btn btn-secondarys" href="{{$image['url']}}" target="_blank">Go to Url</a> 
                                        @if($image['url'] != "")
                                        <a class="btn btn-secondarys" href="{{env('APP_URL')}}/scrapper-python/image/url_list?flagUrl={{$image['id']}}" target="_blank">Go to Link</a>  
                                        @endif
                                        {{ \Carbon\Carbon::parse($image['created_at'])->format('d-m-y') }}
                                    </div>
                                @endif
                            </div>
                        

                        {{-- START - Purpose : Comment Code - DEVTASK-4271 --}}
                        {{-- @php
                        if( $count == 0 || $count == 6){
                            echo '</div>';
                        }
                        @endphp--}}
                        {{-- END - DEVTASK-4271 --}}

                    @endif
                @php $count++;  @endphp
            {{-- @endforeach --}}
            @endforeach
        </div>
        @else
        <div class="col-md-12">No more Images</div>
        @endif
        <br>
        {{ $images->appends(request()->except('page'))->links() }}
    
</div>
<img class="infinite-scroll-products-loader center-block" src="{{env('APP_URL')}}/images/loading.gif" alt="Loading..." style="display: none" />
<!--Remark Modal-->
