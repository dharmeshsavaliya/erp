@foreach($products as $product)
                        <tr>

                            <td><input type="checkbox" name="issue" value="{{ $product->id }}" class="checkBox">
                                {{ $product->id }}</td>
                            <td>@if($product->product) @if (isset($product->product->product_category)) {{ $product->product->product_category->title }} @endif @endif</td> 
                            <td>@if($product->product) {{ $product->product->supplier }} @endif</td> 
                            <td>@if($product->product)  @if ($product->product->brand) {{ $product->product->brands->name }} @endif @endif</td>    
                            <td> <img src="{{ $product->media ? $product->media->getUrl() : '' }}" alt="" onclick="bigImg('{{ $product->media ? $product->media->getUrl() : '' }}')" style="max-width: 150px; max-height: 150px;"></td>
                            <td> <img src="{{ $product->newMedia ? $product->newMedia->getUrl() : '' }}" alt="" height="150" width="150" onclick="bigImg('{{ $product->newMedia ? $product->newMedia->getUrl() : '' }}')"></td>
                            <td>{{ number_format((float)str_replace('0:00:','',$product->speed), 4, '.', '') }} sec</td>
                            <td>{{ $product->updated_at->format('d-m-Y : H:i:s') }}</td>
                            <td>@if($product->product) {{ $product->product->status_id }} @endif 
                                <br>
                                <select class="form-control-sm form-control reject-cropping bg-secondary text-light" name="reject_cropping" data-id="{{ $product->id }}">
                                        <option value="0">Reject Product</option>
                                        <option value="Images Not Cropped Correctly">Images Not Cropped Correctly</option>
                                        <option value="No Images Shown">No Images Shown</option>
                                        <option value="Grid Not Shown">Grid Not Shown</option>
                                        <option value="Blurry Image">Blurry Image</option>
                                        <option value="First Image Not Available">First Image Not Available</option>
                                        <option value="Dimension Not Available">Dimension Not Available</option>
                                        <option value="Wrong Grid Showing For Category">Wrong Grid Showing For Category</option>
                                        <option value="Incorrect Category">Incorrect Category</option>
                                        <option value="Only One Image Available">Only One Image Available</option>
                                        <option value="Image incorrect">Image incorrect</option>
                                </select>
                            </td>
                            <td>{!! $product->getProductIssueStatus($product->id) !!}</td>
                           
                        </tr>
                    @endforeach