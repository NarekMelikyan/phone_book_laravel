<?php

namespace App\Http\Controllers;

use App\Person;
use App\PhoneNumber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PersonsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $persons = Person::with('phoneNumbers')->get();
        return response()->json(['persons' => $persons], 200);
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
        $data = $request->all();
        $rules = [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email|unique:persons',
            'phone' => 'array',
            'phone.*' => 'integer|distinct'
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $person = Person::create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email']
        ]);

        foreach ($data['phone'] as $phone) {
            PhoneNumber::create([
                'person_id' => $person->id,
                'phone_number' => $phone
            ]);
        }

        $created = Person::with('phoneNumbers')->where('id', $person->id)->first();

        return response()->json(['person' => $created], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $person = Person::with('phoneNumbers')->where('id', $id)->first();
        return response()->json(['person' => $person], 200);
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
        $rules = [
            'first_name' => 'required',
            'last_name' => 'required',
            'phone' => 'array',
            'phone.*' => 'integer|distinct'
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $personData = [
            'first_name' => $request->get('first_name'),
            'last_name' => $request->get('last_name')
        ];
        Person::where('id', $id)->update($personData);

        $existingNumbers = $this->getExistingNumbersOfPersonByPersonId($id);
        $newPhoneNumbers = $request->get('phone');

        $phoneNumbersForDelete = array_diff($existingNumbers, $newPhoneNumbers);
        $phoneNumbersForCreate = array_diff($newPhoneNumbers, $existingNumbers);

        foreach ($phoneNumbersForDelete as $item) {
            PhoneNumber::where('phone_number', $item)->delete();
        }

        foreach ($phoneNumbersForCreate as $item) {
            PhoneNumber::create([
                'person_id' => $id,
                'phone_number' => $item
            ]);
        }

        $updated = Person::with('phoneNumbers')->where('id', $id)->first();
        return response()->json(['person' => $updated], 201);
    }

    /**
     * Get existing numbers of a person from a storage.
     *
     * @param  int $person_id
     * @return \Illuminate\Http\Response
     */
    private function getExistingNumbersOfPersonByPersonId(int $person_id)
    {
        $existingNumbersCollection = PhoneNumber::select('phone_number')
            ->where('person_id', $person_id)
            ->get()
            ->toArray();
        $existingNumbers = [];
        foreach ($existingNumbersCollection as $item) {
            $existingNumbers[] = $item["phone_number"];
        }
        return $existingNumbers;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Person::where('id',$id)->delete();
        return response()->json(['Person deleted successfully!'],204);
    }
}
