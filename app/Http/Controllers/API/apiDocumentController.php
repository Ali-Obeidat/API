<?php

namespace App\Http\Controllers\API;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;

class apiDocumentController extends Controller
{
    // Function to get a certain user document 
    public function getUserDocuments(Request $request)
    {
        // get documents from documents table
        $documents = Document::where('user_id', $request['user_id'])->get();
        return  $documents;
    }
    // Function to upload user document 
    public function store(Request $request)
    {
        $Identity1 = $request->file('Identity1');
        $Identity2 = $request->file('Identity2');
        $Address = $request->file('Address');
        if ($request->Address != null) {
            $AddressName = $Address->getClientOriginalName();
            $Address->move(public_path('files'), $AddressName);
        } else {
            $AddressName = null;
        }
        if ($request->Identity1 != null) {
            $Identity1Name = $Identity1->getClientOriginalName();
            $Identity1->move(public_path('files'), $Identity1Name);
        } else {
            $Identity1Name = null;
        }
        if ($request->Identity2 != null) {
            $Identity2Name = $Identity2->getClientOriginalName();
            $Identity2->move(public_path('files'), $Identity2Name);
        } else {
            $Identity2Name = null;
        }

        $imageUpload = new Document();

        $imageUpload->user_id = Auth::user()->id;
        $imageUpload->Identity1 = $Identity1Name;
        $imageUpload->Identity2 = $Identity2Name;
        $imageUpload->Address = $AddressName;
        $imageUpload->document_status = 'processing';
        $imageUpload->save();

        return 'Document was created';
    }

    // Function to let user update there documents while document_status:processing
    public function UserUpdate(Request $request,  $id)
    {
        $Identity1 = $request->file('Identity1');
        $Identity2 = $request->file('Identity2');
        $Address = $request->file('Address');
        // return $Address;
        if ($request->Address != null) {
            $AddressName = $Address->getClientOriginalName();
            $Address->move(public_path('files'), $AddressName);
        } else {
            $AddressName = null;
        }
        if ($request->Identity1 != null) {
            $Identity1Name = $Identity1->getClientOriginalName();
            $Identity1->move(public_path('files'), $Identity1Name);
        } else {
            $Identity1Name = null;
        }
        if ($request->Identity2 != null) {
            $Identity2Name = $Identity2->getClientOriginalName();
            $Identity2->move(public_path('files'), $Identity2Name);
        } else {
            $Identity2Name = null;
        }
        if ($request->Address != null) {
            $document = Document::find($id);
            $Identity1Name = $document->Identity1;
            $Identity2Name = $document->Identity2;
        }
        if ($request->Identity1 != null) {
            $document = Document::find($id);
            $AddressName = $document->Address;
            $Identity2Name = $document->Identity2;
        }
        if ($request->Identity1 != null && $request->Identity2 != null) {
            $document = Document::find($id);
            $AddressName = $document->Address;
            $Identity1Name = $request->Identity1->getClientOriginalName();
            $Identity2Name = $request->Identity2->getClientOriginalName();
        }
        if ($request->Identity2 != null) {
            $document = Document::find($id);
            $AddressName = $document->Address;
            $Identity1Name = $document->Identity1;
        }
        if ($request->Address != null && $request->Identity1 != null) {
            $AddressName = $request->Address->getClientOriginalName();
            $Identity1Name = $request->Identity1->getClientOriginalName();
            $Identity2Name = $request->Identity2->getClientOriginalName();
        }
        $document->Identity1 = $Identity1Name;
        $document->Identity2 = $Identity2Name;
        $document->Address = $AddressName;
        $document->save();
        return 'Document was updated';
    }
}
