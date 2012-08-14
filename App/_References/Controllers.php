<?php

namespace _References;

class Controllers
{
    static function Load()
    {
        $Controller = ucfirst(\F3::resolve("{{@PARAMS.Controller}}"));
        $Action = ucfirst(\F3::resolve("{{@PARAMS.Action}}"));
        $Params = \F3::resolve("{{@PARAMS.Params}}");

        if ($Controller != null) {

            if (file_exists("App/Controllers/" . $Controller . "Controller.php")) {
                if ($Action != null) {
                    if (method_exists("\\Controllers\\" . $Controller . "Controller", "Action_" . $Action)) {

                        call_user_func_array(array("\\Controllers\\" . $Controller . "Controller",
                                "Action_" . $Action), array($Params));
                        \F3::set('RenderBody', $Controller . "/" . $Action . ".html");

                        if (!\F3::get("HideDefaultLayout")) {
                            print (\Template::serve('Shared/_Layout.html'));
                        }
                        if (\F3::get('CustomLayout') && \F3::get("HideDefaultLayout")) {
                            if (file_exists('App/Views/Shared/_' . ucfirst(\F3::get('CustomLayout')) . '.html')) {
                                print (\Template::serve('Shared/_' . ucfirst(\F3::get('CustomLayout')) . '.html'));
                            } else {
                                throw new \Exception("Unable to load Layout: " . ucfirst(\F3::get('CustomLayout')));
                            }
                        }

                    } else {
                        \F3::error(404);
                    }
                } else {
                    if (method_exists("\\Controllers\\" . $Controller . "Controller", "Action_Index")) {
                        if ($Params != null) {
                            call_user_func_array(array("\\Controllers\\" . $Controller . "Controller",
                                    "Action_Index"), array($Params));
                            \F3::set('RenderBody', $Controller . "/Index.html");
                            if (!\F3::get("HideDefaultLayout")) {
                                print (\Template::serve('Shared/_Layout.html'));
                            }
                            if (\F3::get('CustomLayout') && \F3::get("HideDefaultLayout")) {
                                if (file_exists('App/Views/Shared/_' . ucfirst(\F3::get('CustomLayout')) . '.html')) {
                                    print (\Template::serve('Shared/_' . ucfirst(\F3::get('CustomLayout')) . '.html'));
                                } else {
                                    throw new \Exception("Unable to load Layout: " . ucfirst(\F3::get('CustomLayout')));
                                }
                            }
                        } else {
                            call_user_func_array(array("\\Controllers\\" . $Controller . "Controller",
                                    "Action_Index"), array());
                            \F3::set('RenderBody', $Controller . "/Index.html");
                            if (!\F3::get("HideDefaultLayout")) {
                                print (\Template::serve('Shared/_Layout.html'));
                            }
                            if (\F3::get('CustomLayout') && \F3::get("HideDefaultLayout")) {
                                if (file_exists('App/Views/Shared/_' . ucfirst(\F3::get('CustomLayout')) . '.html')) {
                                    print (\Template::serve('Shared/_' . ucfirst(\F3::get('CustomLayout')) . '.html'));
                                } else {
                                    throw new \Exception("Unable to load Layout: " . ucfirst(\F3::get('CustomLayout')));
                                }
                            }
                        }

                    } else {
                        \F3::error(404);
                    }
                }
            } else {
                \F3::error(404);
            }
        } else {
            if (file_exists("App/Controllers/ViewController.php")) {
                if (method_exists("\\Controllers\\ViewController", "Action_Index")) {
                    call_user_func_array(array("\\Controllers\\ViewController", "Action_Index"),
                        array());
                  //  \F3::set('RenderBody', 'View/Index.html');
                  //  print (\Template::serve('Shared/_Layout.html'));
                  \F3::error(404);
                } else {
                    \F3::error(404);
                }
            } else {
                \F3::error(404);
            }
        }
    }

    static function LoadAPI()
    {
        $ControllerAPI = ucfirst(\F3::resolve("{{@PARAMS.ControllerAPI}}"));
        $ActionAPI = ucfirst(\F3::resolve("{{@PARAMS.ActionAPI}}"));
        $ParamsAPI = \F3::resolve("{{@PARAMS.ParamsAPI}}");

        if ($ControllerAPI != null) {

            if (file_exists("App/ControllersAPI/" . $ControllerAPI . "API.php")) {
                if ($ActionAPI != null) {
                    if (method_exists("\\ControllersAPI\\" . $ControllerAPI . "API", "API_" . $ActionAPI)) {
                        call_user_func_array(array("\\ControllersAPI\\" . $ControllerAPI . "API", "API_" .
                                $ActionAPI), array(@$ParamsAPI));
                    } else {
                        \F3::error(404);
                    }
                } else {
                    if (method_exists("\\ControllersAPI\\" . $ControllerAPI . "API", "API_Index")) {
                        call_user_func_array(array("\\ControllersAPI\\" . $ControllerAPI . "API",
                                "API_Index"), array());
                    } else {
                        \F3::error(404);
                    }
                }
            } else {
                \F3::error(404);
            }
        } else {
            if (file_exists("App/ControllersAPI/ViewAPI.php")) {
                if (method_exists("\\ControllersAPI\\ViewAPI", "API_Index")) {
                    call_user_func_array(array("\\ControllersAPI\\ViewAPI", "API_Index"), array());
                } else {
                    \F3::error(404);
                }
            } else {
                \F3::error(404);
            }
        }
    }
}

?>