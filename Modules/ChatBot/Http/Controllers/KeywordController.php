<?php

namespace Modules\ChatBot\Http\Controllers;

use App\Library\Watson\Model as WatsonManager;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use \App\ChatbotKeyword;
use \App\ChatbotKeywordValue;

class KeywordController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {

        $chatKeywords = ChatbotKeyword::leftJoin("chatbot_keyword_values as ckv", "ckv.chatbot_keyword_id", "chatbot_keywords.id")
            ->select("chatbot_keywords.*", \DB::raw("group_concat(ckv.value) as `values`"))
            ->groupBy("chatbot_keywords.id")
            ->orderBy("chatbot_keywords.id","desc")
            ->paginate(10);

        return view('chatbot::keyword.index', compact('chatKeywords'));
    }

    public function create()
    {
        return view('chatbot::keyword.create');
    }

    public function save(Request $request)
    {
        $params            = $request->all();
        $params["keyword"] = str_replace(" ", "_", preg_replace('/\s+/', ' ', $params["keyword"]));

        $validator = Validator::make($params, [
            'keyword' => 'required|unique:chatbot_keywords|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(["code" => 500, "error" => []]);
        }

        $chatbotKeyword = ChatbotKeyword::create($params);
        WatsonManager::pushKeyword($chatbotKeyword->id);

        return response()->json(["code" => 200, "data" => $chatbotKeyword, "redirect" => route("chatbot.keyword.edit", [$chatbotKeyword->id])]);
    }

    public function destroy(Request $request, $id)
    {
        if ($id > 0) {

            $chatbotKeyword = ChatbotKeyword::where("id", $id)->first();

            if ($chatbotKeyword) {
                ChatbotKeywordValue::where("chatbot_keyword_id", $id)->delete();
                $chatbotKeyword->delete();
                WatsonManager::deleteKeyword($chatbotKeyword->id);
                return redirect()->back();
            }

        }

        return redirect()->back();
    }

    public function edit(Request $request, $id)
    {
        $chatbotKeyword = ChatbotKeyword::where("id", $id)->first();

        return view("chatbot::keyword.edit", compact('chatbotKeyword'));
    }

    public function update(Request $request, $id)
    {

        $params                       = $request->all();
        $params["keyword"]            = str_replace(" ", "_", $params["keyword"]);
        $params["chatbot_keyword_id"] = $id;

        $chatbotKeyword = ChatbotKeyword::where("id", $id)->first();

        if ($chatbotKeyword) {

            $chatbotKeyword->fill($params);
            $chatbotKeyword->save();

            $chatbotKeywordValue = new ChatbotKeywordValue;
            $chatbotKeywordValue->fill($params);
            $chatbotKeywordValue->save();

            WatsonManager::pushKeyword($chatbotKeyword->id);

        }

        return redirect()->back();

    }

    public function destroyValue(Request $request, $id, $valueId)
    {
        $cbValue = ChatbotKeywordValue::where("chatbot_keyword_id", $id)->where("id", $valueId)->first();
        if ($cbValue) {
            $cbValue->delete();
            WatsonManager::pushKeyword($id);

        }
        return redirect()->back();
    }

}
