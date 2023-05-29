@if (count($allresources) > 0)
    @foreach ($allresources as $key => $resources)
        <tr>
            <td>{{ $key + 1 }}</td>
            <td><input type="checkbox" value="{{ $resources->id }}" name="id" class="checkBoxClass">
            <td>{{ !empty($resources->category->title) ? $resources->category->title : '' }}</td>
            <td>{{ !empty($resources->category->childs->title) ? $resources->category->childs->title : '' }}</td>
            <td><a href="{{ $resources['url'] }}" title="View Url"
                    target="_blank">{{ isset($resources['url']) ? $resources['url'] : '-' }}</a>
            </td>
            <td> @isset($allresources['image1'])
                    <div class="col-md-6">
                        <img onclick="OpenModel(this.id)" id="myImg1" class="myImg"
                            src="{{ URL::to('/category_images/' . $allresources['image1']) }}"
                            alt="{{ URL::to('/category_images/' . $allresources['image1']) }}"
                            style="width: 50% !important;height: 50px !important;">
                    </div>
                @endisset
                @isset($allresources['image2'])
                    <div class="col-md-6">
                        <img onclick="OpenModel(this.id)" id="myImg2" class="myImg"
                            src="{{ URL::to('/category_images/' . $allresources['image2']) }}"
                            alt="{{ URL::to('/category_images/' . $allresources['image2']) }}"
                            style="width: 50% !important;height: 50px !important;">
                    </div>
                @endisset
                @isset($resources['images'])
                    @if ($resources['images'] != null)
                        @foreach (json_decode($resources['images']) as $image)
                            <div class="col-md-6" style="margin-top: 15px">
                                <img id="myShowImg" img-id='{{ $resources['id'] }}'
                                    src="{{ URL::to('/category_images/' . $image) }}"
                                    style="width: 50% !important;height: 50px !important;">
                            </div>
                        @endforeach
                    @endif
                @endisset
            </td>
            <td>{{ date('l, d/m/Y', strtotime($resources['updated_at'])) }}</td>
            <td>{{ ucwords($resources['created_by']) }}</td>
        </tr>
    @endforeach
@else
    <tr>
        <td class="text-center" colspan="8">No Record found.</td>
    </tr>
@endif