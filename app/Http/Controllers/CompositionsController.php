<?php

namespace App\Http\Controllers;

use App\Compositions;
use Illuminate\Http\Request;

class CompositionsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $matchedArray = [];
        $compositions = Compositions::query();

        if($request->keyword != null){
            //getting search results based on two words 
            $comps = $compositions->where("name","LIKE","%{$request->term}%")->get();
            foreach ($comps as $comp) {
                $searchWord = $request->keyword;
                $searchWordArray = explode(' ', $searchWord);
                if(count($searchWordArray) != 0){
                    $isMatched = 1;
                    foreach ($searchWordArray as $word) {
                        if (strpos($comp->name, $word) !== false) {
                            
                        }else{
                            $isMatched = 0;
                        }
                    }
                    if($isMatched == 1){
                        $matchedArray[] = $comp->id;
                    }
                }
            }
        }
        

        
        if($request->keyword != null) {
            $compositions = $compositions->whereIn('id',$matchedArray);
        }

        $listcompostions = ["" => "-- Select --"] + Compositions::where('replace_with','!=','')->groupBy('replace_with')->pluck('replace_with','replace_with')->toArray();

        if($request->with_ref == 1) {
            $compositions = $compositions->where(function($q) use ($request) {
                $q->orWhere('replace_with',"!=",'')->WhereNotNull('replace_with');
            });
        }else{
            $compositions = $compositions->where(function($q) use ($request) {
                $q->orWhere('replace_with','')->orWhereNull('replace_with');
            });
        }

        $compositions = $compositions->orderBy('id','desc')->paginate(12);

        return view('compositions.index', compact('compositions','listcompostions'));
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
        $this->validate($request, [
            'name'         => 'required',
            'replace_with' => 'required',
        ]);

        $c = Compositions::create($request->all());

        return redirect()->back();

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Compositions  $compositions
     * @return \Illuminate\Http\Response
     */
    public function show(Compositions $compositions)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Compositions  $compositions
     * @return \Illuminate\Http\Response
     */
    public function edit(Compositions $compositions)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Compositions  $compositions
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Compositions $compositions, $id)
    {
        //
        $c = $compositions->find($id);
        if ($c) {
            $c->fill($request->all());
            $c->save();
        }

        if ($request->ajax()) {
            return response()->json(["code" => 200 , "data" => []]);
        }   

        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Compositions  $compositions
     * @return \Illuminate\Http\Response
     */
    public function destroy(Compositions $compositions, $id)
    {
        //
        $compositions->find($id)->delete();

        return redirect()->back();
    }

    public function usedProducts(Compositions $compositions, Request $request,  $id)
    {
        $compositions = $compositions->find($id);

        if($compositions) {
            // check the type and then 
           $name = '"'.$compositions->name.'"';
           $products = \App\ScrapedProducts::where("properties","like",'%'.$name.'%')->latest()->limit(5)->get();

           $view = (string)view("compositions.preview-products",compact('products'));
           return response()->json(["code" => 200, "html" => $view]);
        }

        return response()->json(["code" => 200, "html" => ""]);

    }

    public function affectedProduct(Request $request)
    {
        $from = $request->from;
        $to   = $request->to;

        if (!empty($from) && !empty($to)) {
            // check the type and then
            $q     = '"'.$from.'"';
            $total = \App\ScrapedProducts::where("properties", "like", '%' . $q . '%')
                ->join("products as p", "p.sku", "scraped_products.sku")
                ->where("p.composition", "")
                ->groupBy("p.id")
                ->get()->count();

            $view = (string) view("compositions.partials.affected-products", compact('total', 'from', 'to'));

            return response()->json(["code" => 200, "html" => $view]);
        }
    }

    public function updateComposition(Request $request)
    {
        $from = $request->from;
        $to   = $request->to;

        $updateWithProduct = $request->with_product;
        if ($updateWithProduct == "yes") {
            \App\Jobs\UpdateProductCompositionFromErp::dispatch([
                "from"    => $from,
                "to"      => $to,
                "user_id" => \Auth::user()->id,
            ])->onQueue("supplier_products");
        }

        $c = Compositions::where("name",$from)->first();
        if($c) {
            $c->replace_with = $to;
            $c->save();
        }

        return response()->json(["code" => 200, "message" => "Your request has been pushed successfully"]);
    }

    public function replaceComposition(Request $request){
        $from = $request->name;
        $to   = $request->replace_with;
        if(!empty($from) && !empty($to)){
            $products = \App\Product::where('composition','LIKE','%'.$from.'%')->get();
            
            if($products){
                foreach ($products as $product) {
                    $composition = $product->composition;
                    $replaceWords = [];
                    $replaceWords[] = ucwords($from);
                    $replaceWords[] = strtoupper($from);
                    $replaceWords[] = strtolower($from);
                    $newComposition = str_replace($replaceWords,$to,$composition);
                    $product->composition = $newComposition;
                    $product->update();
                }

                $c = Compositions::where("name",$from)->first();
                if($c) {
                    $c->replace_with = $to;
                    $c->save();
                }else{
                    if(!empty($from)){
                        $comp = new Compositions();
                        $comp->name = $from;
                        $comp->replace_with = $to;
                        $comp->save(); 
                    }          
                }
            }
        }
        return redirect()->back();
    }
}
