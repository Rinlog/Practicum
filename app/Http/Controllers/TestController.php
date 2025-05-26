<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;


class TestController extends Controller
{

    function Test($id){

    
        return "you entered the id $id";
    }

    Function ViewTestTable(){
        $testTable = DB::select("select * from testtable");
        print_r($testTable[0]->testid . "<br>");
        print_r($testTable[0]->testname . "<br>");
        print_r($testTable);
    }
}
