<?php

/* default/home.twig */
class __TwigTemplate_f75283e6835d9499ab980626b410b036c0d2dcf9b295eb251bcbc30dc7a8ea9f extends Twig_Template
{
    private $source;

    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = array(
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 1
        echo "<!DOCTYPE html>
<html lang=\"en\">
<head>
    <meta charset=\"UTF-8\">
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
    <meta http-equiv=\"X-UA-Compatible\" content=\"ie=edge\">
    <title>Cube</title>
    ";
        // line 8
        echo css("styles");
        echo "
</head>
<body>
    <h1 class=\"title\">
        ";
        // line 12
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, (isset($context["env"]) || array_key_exists("env", $context) ? $context["env"] : (function () { throw new Twig_Error_Runtime('Variable "env" does not exist.', 12, $this->source); })()), "project_name", array()), "html", null, true);
        echo "
    </h1>
    <div class=\"cube-welcome\">
        Welcome to PHP Cube.
    </div>
    <div class=\"cube-version\">
        v";
        // line 18
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, (isset($context["env"]) || array_key_exists("env", $context) ? $context["env"] : (function () { throw new Twig_Error_Runtime('Variable "env" does not exist.', 18, $this->source); })()), "cube_version", array()), "html", null, true);
        echo "
    </div>
    <div class=\"cube-docs\">
        <a target=\"_blank\" href=\"https://bitbucket.org/brainex/php-cube/src/master/readme.md\">Read the documentation</a>
    </div>
</body>
</html>";
    }

    public function getTemplateName()
    {
        return "default/home.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  48 => 18,  39 => 12,  32 => 8,  23 => 1,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "default/home.twig", "/opt/lampp/htdocs/utils/cube/app/views/default/home.twig");
    }
}
