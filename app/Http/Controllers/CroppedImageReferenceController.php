<?php

namespace App\Http\Controllers;

use App\Brand;
use App\Category;
use App\CroppedImageReference;
use App\Helpers\StatusHelper;
use App\Product;
use App\Supplier;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

class CroppedImageReferenceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products = CroppedImageReference::with(['media', 'newMedia'])->orderBy('id', 'desc')->paginate(50);

        return view('image_references.index', compact('products'));
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\CroppedImageReference  $croppedImageReference
     * @return \Illuminate\Http\Response
     */
    public function show(CroppedImageReference $croppedImageReference)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\CroppedImageReference  $croppedImageReference
     * @return \Illuminate\Http\Response
     */
    public function edit(CroppedImageReference $croppedImageReference)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\CroppedImageReference  $croppedImageReference
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CroppedImageReference $croppedImageReference)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\CroppedImageReference  $croppedImageReference
     * @return \Illuminate\Http\Response
     */
    public function destroy(CroppedImageReference $croppedImageReference)
    {
        //
    }

    public function grid(Request $request)
    {
        $query = CroppedImageReference::with(['differentWebsiteImages', 'product', 'httpRequestData.requestData', 'product.product_category'])->join('products', 'products.id', 'cropped_image_references.product_id');

        if ($request->category || $request->brand || $request->supplier || $request->crop || $request->status || $request->filter_id) {
            if (is_array(request('category'))) {
                if (request('category') != null && request('category')[0] != 1) {
                    // $query->whereHas('product', function ($qu) use ($request) {
                    //     $qu->whereIn('category', request('category'));
                    // });
                    $query->whereIn('products.category', request('category'));
                }
            } else {
                if (request('category') != null && request('category') != 1) {
                    // $query->whereHas('product', function ($qu) use ($request) {
                    //     $qu->where('category', request('category'));
                    // });
                    $query->where('products.category', request('category'));
                }
            }

            if (isset($request->filter_id) && $request->filter_id) {
                // $query->whereHas('product', function ($qu) use ($request) {
                //     $qu->where('id', $request->filter_id);
                // });
                $query->where('products.id', $request->filter_id);
            }

            if (request('brand') != null && $request->brand) {
                // $query->whereHas('product', function ($qu) use ($request) {
                //     $qu->whereIn('brand', request('brand'));
                // });
                $query->whereIn('products.brand', request('brand'));
            }

            if (request('supplier') != null) {
                // $query->whereHas('product', function ($qu) use ($request) {
                //     $qu->whereIn('supplier', request('supplier'));
                // });
                $query->whereIn('products.supplier', request('supplier'));
            }

            if (request('status') != null && request('status') != 0) {
                // $query->whereHas('product', function ($qu) use ($request) {
                //     $qu->where('status_id', request('status'));
                // });
                $query->where('products.status_id', request('status'));
            } else {
                // $query->whereHas('product', function ($qu) use ($request) {
                //     $qu->where('status_id', '!=', StatusHelper::$cropRejected);
                // });
                $query->where('products.status_id', '!=', StatusHelper::$cropRejected);
            }

            if (request('crop') != null) {
                if (request('crop') == 2) {
                    $query->whereNotNull('cropped_image_references.new_media_id');
                } elseif (request('crop') == 3) {
                    $query->whereNull('cropped_image_references.new_media_id');
                }
            }
            $products = $query->select(['cropped_image_references.*'])->orderBy('cropped_image_references.id', 'desc')->paginate(50);
        } else {
            // $query->whereHas('product', function ($qu) use ($request) {
            //     $qu->where('status_id', '!=', StatusHelper::$cropRejected);
            // });

            $query->where('products.status_id', '!=', StatusHelper::$cropRejected);

            $products = $query->select(['cropped_image_references.*'])->orderBy('cropped_image_references.id', 'desc')
                ->groupBy('cropped_image_references.original_media_id')
              ->with(['media', 'newMedia', 'differentWebsiteImages' => function ($q) {
                  $q->with('newMedia');
              }])
                ->paginate(50);
        }
        $total = $products->count();

        $pendingProduct = Product::where('status_id', StatusHelper::$autoCrop)->where('stock', '>=', 1)->count();

        $pendingCategoryProduct = Product::where('status_id', StatusHelper::$attributeRejectCategory)->where('stock', '>=', 1)->count();

        if (request('customer_range') != null) {
            $dateArray = explode('-', request('customer_range'));
            $startDate = trim($dateArray[0]);
            $endDate = trim(end($dateArray));
            if ($startDate == '1995/12/25') {
                $totalCounts = CroppedImageReference::where('created_at', '>=', \Carbon\Carbon::now()->subHour())->count();
            } elseif ($startDate == $endDate) {
                $totalCounts = CroppedImageReference::whereDate('created_at', '=', end($dateArray))->count();
            } else {
                $totalCounts = CroppedImageReference::whereBetween('created_at', [$startDate, $endDate])->count();
            }

            if ($request->ajax()) {
                return response()->json([
                    'count' => $totalCounts,
                ], 200);
            }
        } else {
            $totalCounts = 0;
        }

        // dd($products);

        if ($request->ajax()) {
            return response()->json([
                'tbody' => view('image_references.partials.griddata', compact('products', 'total', 'pendingProduct', 'totalCounts', 'pendingCategoryProduct'))->render(),
                'links' => $products,
                'total' => $total,
            ], 200);
        }

        return view('image_references.grid', compact('products', 'total', 'pendingProduct', 'totalCounts', 'pendingCategoryProduct'));
    }

    public function rejectCropImage(Request $request)
    {
        $reference = CroppedImageReference::find($request->id);
        $product = Product::find($reference->product_id);
        dd($product);
    }

    public function getCategories(Request $request)
    {
        $category_selection = Category::attr(['name' => 'category[]', 'class' => 'form-control select-multiple2', 'id' => 'category'])
            ->renderAsArray();
        $answer = $this->setByParent($category_selection);

        return response()->json(['result' => $answer]);
    }

    public function getProductIds(Request $request)
    {
        $response = Product::select('id')->get();

        return response()->json(['result' => $response]);
    }

    public function getBrands(Request $request)
    {
        $response = Brand::select(['id', 'name as text'])->get()->toArray();

        return response()->json(['result' => [['text' => 'Brands', 'children' => $response]]]);
    }

    public function getSupplier(Request $request)
    {
        $response = Supplier::select(['id', 'supplier as text'])->get();

        return response()->json(['result' => [['text' => 'Suppliers', 'children' => $response]]]);
    }

    private function setByParent($data, $step = 0, &$result = [])
    {
        $nbsp = '';
        if ($step) {
            for ($i = 0; $i < $step * 2; $i++) {
                $nbsp .= '&nbsp;';
            }
        }

        foreach ($data as $value) {
            $result[] = [
                'id' => $value['id'],
                'text' => $nbsp.$value['title'],
            ];
            if (! empty($value['child'])) {
                $this->setByParent($value['child'], $step + 1, $result);
            }
        }

        return $result;
    }

    public function manageInstance(Request $request)
    {
        $instances = \App\CroppingInstance::all();

        return view('image_references.partials.manage-instance', compact('instances'));
    }

    public function loginstance(Request $request)
    {
        $url = 'http://173.212.203.50:100/get-logs';
        $date = $request->date;
        $id = $request->id;

        $data = ['instance_id' => $id, 'date' => $date];
        $data = json_encode($data);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'accept: application/json']);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $result = curl_exec($ch);
        echo $result;
    }

    public function addInstance(Request $request)
    {
        $params = $request->except('_token');
        \App\CroppingInstance::create($params);

        $instances = \App\CroppingInstance::all();

        return view('image_references.partials.manage-instance', compact('instances'));
    }

    public function deleteInstance(Request $request)
    {
        \App\CroppingInstance::find($request->id)->delete();
        $instances = \App\CroppingInstance::all();

        return view('image_references.partials.manage-instance', compact('instances'));
    }

    public function startInstance(Request $request)
    {
        $instance = \App\CroppingInstance::find($request->id);
        if ($instance) {
            $client = new Client();
            $response = $client->request('POST', config('constants.py_crop_script').'/start', [
                'form_params' => [
                    'instanceId' => $instance->instance_id,
                ],
            ]);

            return response()->json(['code' => 200, 'message' => (string) $response->getBody()->getContents()]);
        } else {
            return response()->json(['code' => 500, 'message' => 'No instance id found']);
        }
    }

    public function stopInstance(Request $request)
    {
        $instance = \App\CroppingInstance::find($request->id);
        if ($instance) {
            $client = new Client();
            $response = $client->request('POST', config('constants.py_crop_script').'/stop', [
                'form_params' => [
                    'instanceId' => $instance->instance_id,
                ],
            ]);

            return response()->json(['code' => 200, 'message' => (string) $response->getBody()->getContents()]);
        } else {
            return response()->json(['code' => 500, 'message' => 'No instance id found']);
        }
    }
}
