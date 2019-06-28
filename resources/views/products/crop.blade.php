@extends('layouts.app')

@section('large_content')
    <div class="row">
        <div class="col-md-12">
            <h5 class="page-heading">
                Crop Image Approval <a href="{{ action('ProductController@showSOP') }}?type=Crop" class="pull-right">SOP</a>
            </h5>
            <div class="row">
                <div class="col-md-12">
                    <table class="table table-striped table-bordered">
                        <tr>
                            <td>
                                <strong>{{ $product->name }}</strong>
                                <br>{{ $product->sku }}
                                <br><strong>{{ $product->product_category->title }}</strong>
                            </td>
                            <td>
                                <form action="{{ action('ProductCropperController@rejectCrop', $product->id) }}">
                                    <a href="{{ action('ProductCropperController@approveCrop', $product->id) }}" type="button" class="btn btn-secondary approvebtn">Approve</a>
                                    <br><br>
                                    <select name="remark" id="remark">
                                        <option value="Images Not Cropped Correctly">Images Not Cropped Correctly</option>
                                        <option value="No Images Shown">No Images Shown</option>
                                        <option value="Grid Not Shown">Grid Not Shown</option>
                                        <option value="First Image Not Available">First Image Not Available</option>
                                        <option value="Dimension Not Available">Dimension Not Available</option>
                                        <option value="Wrong Grid Showing For Category">Wrong Grid Showing For Category</option>
                                        <option value="Incorrect Category">Incorrect Category</option>
                                        <option value="Only One Image Available">Only One Image Available</option>
                                    </select>
{{--                                    <input type="text" class="form-control" placeholder="Remark..." name="remark" id="remark">--}}
                                    <button class="btn btn-danger">Reject</button>
                                    <br>
                                    @if($secondProduct)
{{--                                        <a href="{{ action('ProductCropperController@showImageToBeVerified', $secondProduct->id) }}">Next Image</a>--}}
                                    @endif
                                </form>
                            </td>
                            <td>
                                <strong>Dimension: {{round($product->lmeasurement*0.393701)}} X {{round($product->hmeasurement*0.393701)}} X {{round($product->dmeasurement*0.393701)}}</strong>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-12 text-center">
        </div>
        <div class="col-md-12">
            <div style="margin: 0 auto; width: 100%">
                <form action="{{ action('ProductCropperController@ammendCrop', $product->id) }}" method="post">
                    @csrf
                    @foreach($product->media()->get() as $image)
                        <?php
//                        [$height, $width] = getimagesize($image->getUrl())
                        ?>
                        @if (stripos($image->filename, 'cropped') !== false)
                            <div style="display: inline-block; border: 1px solid #ccc" class="mt-5">
                                <div style="width: 80%; margin: 5px auto;">
                                    <input type="hidden" name="url[{{$image->filename}}]" value="{!! $image->getUrl() !!}">
                                    <input type="hidden" name="mediaIds[{{$image->filename}}]" value="{!! $image->id !!}">
                                    <div class="form-group">
                                        <select class="form-control avoid-approve" style="width: 100px; !important;" name="size[{{$image->filename}}]" id="size">
                                            <option value="ok">Select Sizes...</option>
                                            <optgroup label="Model Image">
                                                <option value="H812">HEIGHT - 812</option>
                                                <option value="H848">HEIGHT - 848</option>
                                                <option value="H804">HEIGHT - 804</option>
                                            </optgroup>
                                            @if($img=='Backpack.png')
                                                <optgroup label="Backpacks">
                                                    <option value="H257">HEIGHT - 257</option>
                                                    <option value="H326">HEIGHT - 326</option>
                                                    <option value="H366">HEIGHT - 366</option>
                                                    <option value="H471">HEIGHT - 471</option>
                                                    <option value="H540">HEIGHT - 540</option>
                                                    <option value="H612">HEIGHT - 612</option>
                                                    <option value="H677">HEIGHT - 677</option>
                                                    <option value="H744">HEIGHT - 744</option>
                                                </optgroup>
                                            @endif
                                            @if($img=='belt.png')
                                                <optgroup label="Belts">
                                                    <option value="W738">Width - 738</option>
                                                </optgroup>
                                            @endif
                                            @if($img=='Clothing.png')
                                                <optgroup label="Clothing">
                                                    <option value="H790">HEIGHT- 790</option>
                                                </optgroup>
                                            @endif
                                            @if($img=='shoes_grid.png')
                                                <optgroup label="Shoes">
                                                    <option value="W720">WIDTH - 720</option>
                                                </optgroup>
                                            @endif
                                            @if($img=='bow.png')
                                                <optgroup label="Bow">
                                                    <option value="W738">WIDTH - 738</option>
                                                </optgroup>
                                            @endif
                                            @if($img=='Hair_accessories.png')
                                                <optgroup label="Hair Accessories">
                                                    <option value="W606">WIDTH - 606</option>
                                                </optgroup>
                                            @endif
                                            @if($img=='Jewellery.png')
                                                <optgroup label="Jewelry">
                                                    <option value="W606">WIDTH - 606</option>
                                                </optgroup>
                                            @endif
                                            @if($img=='Wallet.png')
                                                <optgroup label="Wallet">
                                                    <option value="H210">HEIGHT - 210</option>
                                                    <option value="H290">HEIGHT - 290</option>
                                                    <option value="H353">HEIGHT - 353</option>
                                                    <option value="H448">HEIGHT - 448</option>
                                                </optgroup>
                                            @endif
                                            @if($img=='Tote.png')
                                                <optgroup label="Tote Bags">
                                                    <option value="H252">HEIGHT - 252</option>
                                                    <option value="H326">HEIGHT - 326</option>
                                                    <option value="H404">HEIGHT - 404</option>
                                                    <option value="H470">HEIGHT - 470</option>
                                                    <option value="H540">HEIGHT - 540</option>
                                                    <option value="H606">HEIGHT - 606</option>
                                                    <option value="H678">HEIGHT - 678</option>
                                                    <option value="H742">HEIGHT - 742</option>
                                                </optgroup>
                                            @endif
                                            @if($img=='Sunglasses.png')
                                                <optgroup label="Sunglasses">
                                                    <option value="H235">HEIGHT - 235</option>
                                                    <option value="H442">HEIGHT - 442</option>
                                                </optgroup>
                                            @endif
                                            @if($img=='Shoulder_bag.png')
                                                <optgroup label="Shoulder Bags">
                                                    <option value="H256">HEIGHT - 256</option>
                                                    <option value="H316">HEIGHT - 316</option>
                                                    <option value="H380">HEIGHT - 380</option>
                                                    <option value="H446">HEIGHT - 446</option>
                                                </optgroup>
                                            @endif
                                            @if($img=='Shawl.png')
                                                <optgroup label="Shawl">
                                                    <option value="H540">HEIGHT - 540</option>
                                                    <option value="H610">HEIGHT - 610</option>
                                                    <option value="H742">HEIGHT - 742</option>
                                                </optgroup>
                                            @endif
                                            @if($img=='Handbag.png')
                                                <optgroup label="Handbags">
                                                    <option value="H252">HEIGHT - 252</option>
                                                    <option value="H404">HEIGHT - 404</option>
                                                    <option value="H542">HEIGHT - 542</option>
                                                    <option value="H694">HEIGHT - 694</option>
                                                </optgroup>
                                            @endif
                                            @if($img=='Clutch.png')
                                                <optgroup label="Clutch">
                                                    <option value="H525">HEIGHT - 252</option>
                                                    <option value="H322">HEIGHT - 322</option>
                                                    <option value="H382">HEIGHT - 382</option>
                                                    <option value="H443">HEIGHT - 443</option>
                                                </optgroup>
                                            @endif
                                            @if($img=='Keychains.png')
                                                <optgroup label="Keychain">
                                                    <option value="H470">HEIGHT - 470</option>
                                                </optgroup>
                                            @endif
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <select class="form-control avoid-approve" style="width: 100px; !important;" name="padding[{{$image->filename}}]" id="padding">
                                            <option value="ok">Padding...</option>
                                            <option value="96">96</option>
                                            <option value="121">121</option>
                                            <option value="108">108</option>
                                        </select>
                                    </div>
                                </div>
                                <div style=" margin-bottom: 5px; width: 500px;height: 500px; background-image: url('{{$image->getUrl()}}'); background-size: 500px">
                                    <img style="width: 500px;" src="{{ asset('images/'.$img) }}" alt="">
                                </div>
                            </div>
                        @endif
                    @endforeach
                    <div style="position: fixed; width: 100px; height: 200px; bottom: 218px; right: 85px;">
                        <button class="btn btn-secondary btn-lg">Update Cropped <br>Images</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <!-- Fotorama from CDNJS, 19 KB -->
    <link  href="https://cdnjs.cloudflare.com/ajax/libs/fotorama/4.6.4/fotorama.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fotorama/4.6.4/fotorama.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@8"></script>
    <script>
        $(document).ready(function() {
            $('.avoid-approve').change(function() {
                $('.approvebtn').fadeOut();
            });
        });
    </script>
    @if (Session::has('mesage'))
        <script>
            Swal.fire(
                'Success',
                '{{Session::get('message')}}',
                'success'
            )
        </script>
    @endif
@endsection