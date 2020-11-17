<?php

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Extension\SandboxExtension;
use Twig\Markup;
use Twig\Sandbox\SecurityError;
use Twig\Sandbox\SecurityNotAllowedTagError;
use Twig\Sandbox\SecurityNotAllowedFilterError;
use Twig\Sandbox\SecurityNotAllowedFunctionError;
use Twig\Source;
use Twig\Template;

/* Block/categly_all.twig */
class __TwigTemplate_365b0fc1ff2660691ce029a059c8f00fe716e3cfcd5700d61c2b00bf01f5c559 extends \Eccube\Twig\Template
{
    private $source;
    private $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = [
        ];
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 1
        echo "これは子カテゴリーまで
";
        // line 2
        $context["cate_childs"] = twig_get_attribute($this->env, $this->source, ($context["Category"] ?? null), "getDescendants", [], "any", false, false, false, 2);
        // line 3
        if (($context["cate_childs"] ?? null)) {
            // line 4
            $context["h"] = (twig_get_attribute($this->env, $this->source, ($context["Category"] ?? null), "hierarchy", [], "any", false, false, false, 4) + 1);
            // line 5
            echo "<div id=\"cate_list\">
<h2 class=\"cate_h2\">";
            // line 6
            echo twig_escape_filter($this->env, ($context["subtitle"] ?? null), "html", null, true);
            echo "　カテゴリー一覧</h2>
<ul class=\"cate_list\">
";
            // line 8
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable(($context["cate_childs"] ?? null));
            foreach ($context['_seq'] as $context["_key"] => $context["cate"]) {
                // line 9
                echo "    ";
                if ((($context["h"] ?? null) == twig_get_attribute($this->env, $this->source, $context["cate"], "hierarchy", [], "any", false, false, false, 9))) {
                    // line 10
                    echo "    <li><a href=\"/products/list?category_id=";
                    echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["cate"], "id", [], "any", false, false, false, 10), "html", null, true);
                    echo "\">";
                    echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["cate"], "name", [], "any", false, false, false, 10), "html", null, true);
                    echo "</a></li>
    ";
                }
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['cate'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 13
            echo "</ul>
</div>
";
        }
        // line 16
        echo "
これは孫カテゴリーまで
";
        // line 18
        $context["cate_childs"] = twig_get_attribute($this->env, $this->source, ($context["Category"] ?? null), "getDescendants", [], "any", false, false, false, 18);
        // line 19
        if (($context["cate_childs"] ?? null)) {
            // line 20
            echo "<div id=\"cate_list\">
<h2 class=\"cate_h2\">";
            // line 21
            echo twig_escape_filter($this->env, ($context["subtitle"] ?? null), "html", null, true);
            echo "　カテゴリー一覧</h2>
<ul class=\"cate_list\">
";
            // line 23
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable(($context["cate_childs"] ?? null));
            foreach ($context['_seq'] as $context["_key"] => $context["cate"]) {
                // line 24
                echo "  <li><a href=\"/products/list?category_id=";
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["cate"], "id", [], "any", false, false, false, 24), "html", null, true);
                echo "\">";
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["cate"], "name", [], "any", false, false, false, 24), "html", null, true);
                echo "</a></li>
";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['cate'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 26
            echo "</ul>
</div>
";
        }
    }

    public function getTemplateName()
    {
        return "Block/categly_all.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  109 => 26,  98 => 24,  94 => 23,  89 => 21,  86 => 20,  84 => 19,  82 => 18,  78 => 16,  73 => 13,  61 => 10,  58 => 9,  54 => 8,  49 => 6,  46 => 5,  44 => 4,  42 => 3,  40 => 2,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "Block/categly_all.twig", "C:\\xampp\\htdocs\\ec\\eccube-4.0.5\\app\\template\\default\\Block\\categly_all.twig");
    }
}
