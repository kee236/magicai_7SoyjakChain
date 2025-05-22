# SoyjakAI

--- 
    public function important_feature($redirect=true)
    {


        return true;
    }


    public function credential_check()
    {

        return view('auth.credential-check');
    }

    public function credential_check_action(Request $request)
    {

    }

    function get_general_content_with_checking($url,$proxy="")
    {


        return json_encode($response);

    }



    public function restricted_access(){
        $data = [
            "error_title" => "Demo Restriction",
            "error_code" => "Demo Restriction",
            "error_message" => "This feature has been disabled in this demo version. We recommend to sign up as user and check."
        ];
        return view("errors.custom", $data);
    }