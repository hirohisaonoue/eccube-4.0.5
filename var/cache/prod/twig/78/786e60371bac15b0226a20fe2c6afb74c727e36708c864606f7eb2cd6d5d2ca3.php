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

/* @admin/Content/layout_list.twig */
class __TwigTemplate_695d6e9a705f5c6340216abeb69940e3bb2330abe518350ac083ea2082c1cf03 extends \Eccube\Twig\Template
{
    private $source;
    private $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->blocks = [
            'title' => [$this, 'block_title'],
            'sub_title' => [$this, 'block_sub_title'],
            'javascript' => [$this, 'block_javascript'],
            'main' => [$this, 'block_main'],
        ];
    }

    protected function doGetParent(array $context)
    {
        // line 11
        return "@admin/default_frame.twig";
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 13
        $context["menus"] = [0 => "content", 1 => "layout"];
        // line 11
        $this->parent = $this->loadTemplate("@admin/default_frame.twig", "@admin/Content/layout_list.twig", 11);
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 15
    public function block_title($context, array $blocks = [])
    {
        $macros = $this->macros;
        echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\TranslationExtension']->trans("admin.content.layout_management"), "html", null, true);
    }

    // line 16
    public function block_sub_title($context, array $blocks = [])
    {
        $macros = $this->macros;
        echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\TranslationExtension']->trans("admin.content.contents_management"), "html", null, true);
    }

    // line 18
    public function block_javascript($context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 19
        echo "    <script>
        \$(function() {
            // 削除モーダルのhrefとmessageの変更
            \$('#DeleteModal').on('shown.bs.modal', function(event) {
                var target = \$(event.relatedTarget);
                // hrefの変更
                \$(this).find('[data-method=\"delete\"]').attr('href', target.data('url'));

                // messageの変更
                \$(this).find('p.modal-message').text(target.data('message'));
            });
        });
    </script>
";
    }

    // line 34
    public function block_main($context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 35
        echo "    <div class=\"c-contentsArea__cols\">
        <div class=\"c-contentsArea__primaryCol\">
            <div class=\"c-primaryCol\">
                <div class=\"d-block mb-3\">
                    <a class=\"btn btn-ec-regular\" href=\"";
        // line 39
        echo $this->extensions['Eccube\Twig\Extension\IgnoreRoutingNotFoundExtension']->getUrl("admin_content_layout_new");
        echo "\">";
        echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\TranslationExtension']->trans("admin.common.create__new"), "html", null, true);
        echo "</a>
                </div>
                ";
        // line 42
        echo "                ";
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(($context["Layouts"] ?? null));
        foreach ($context['_seq'] as $context["_key"] => $context["Layout"]) {
            // line 43
            echo "                    <div class=\"card rounded border-0 mb-4\">
                        <div class=\"card-header\">
                            <div class=\"row\">
                                <div class=\"col-8\">
                                    ";
            // line 47
            if ((twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, $context["Layout"], "DeviceType", [], "any", false, false, false, 47), "id", [], "any", false, false, false, 47) == twig_constant("Eccube\\Entity\\Master\\DeviceType::DEVICE_TYPE_PC"))) {
                // line 48
                echo "                                        ";
                $context["icon"] = "fa-desktop";
                // line 49
                echo "                                    ";
            } else {
                // line 50
                echo "                                        ";
                $context["icon"] = "fa-mobile";
                // line 51
                echo "                                    ";
            }
            // line 52
            echo "                                    <i class=\"fa fa-fw ";
            echo twig_escape_filter($this->env, ($context["icon"] ?? null), "html", null, true);
            echo " fa-lg mr-2\"></i>
                                    <a class=\"card-title align-middle\"
                                       href=\"";
            // line 54
            echo twig_escape_filter($this->env, $this->extensions['Eccube\Twig\Extension\IgnoreRoutingNotFoundExtension']->getUrl("admin_content_layout_edit", ["id" => twig_get_attribute($this->env, $this->source, $context["Layout"], "id", [], "any", false, false, false, 54)]), "html", null, true);
            echo "\">";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["Layout"], "name", [], "any", false, false, false, 54), "html", null, true);
            echo "</a>
                                </div>
                                <div class=\"col-4 text-right\">
                                    ";
            // line 57
            if ((twig_get_attribute($this->env, $this->source, $context["Layout"], "isDefault", [], "method", false, false, false, 57) == false)) {
                // line 58
                echo "                                        <button class=\"btn btn-ec-sub mr-3\" type=\"button\" data-toggle=\"modal\" data-target=\"#DeleteModal\"
                                                data-url=\"";
                // line 59
                echo twig_escape_filter($this->env, $this->extensions['Eccube\Twig\Extension\IgnoreRoutingNotFoundExtension']->getUrl("admin_content_layout_delete", ["id" => twig_get_attribute($this->env, $this->source, $context["Layout"], "id", [], "any", false, false, false, 59)]), "html", null, true);
                echo "\"
                                                data-message=\"";
                // line 60
                echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\TranslationExtension']->trans("admin.common.delete_modal__message", ["%name%" => twig_get_attribute($this->env, $this->source, $context["Layout"], "name", [], "any", false, false, false, 60)]), "html", null, true);
                echo "\">
                                            <i class=\"fa fa-close fa-lg text-secondary\" aria-hidden=\"true\"></i>
                                            ";
                // line 62
                echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\TranslationExtension']->trans("admin.content.layout_delete"), "html", null, true);
                echo "
                                        </button>
                                    ";
            }
            // line 65
            echo "                                    <a href=\"#layout-";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["Layout"], "id", [], "any", false, false, false, 65), "html", null, true);
            echo "\" data-toggle=\"collapse\" aria-expanded=\"false\" aria-controls=\"layout-";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["Layout"], "id", [], "any", false, false, false, 65), "html", null, true);
            echo "\">
                                        <i class=\"fa fa-angle-down fa-lg\"></i>
                                    </a>
                                </div>
                            </div>
                        </div>

                        ";
            // line 73
            echo "                        ";
            if ((twig_length_filter($this->env, twig_get_attribute($this->env, $this->source, $context["Layout"], "pages", [], "any", false, false, false, 73)) > 0)) {
                // line 74
                echo "                            <div class=\"collapse ec-cardCollapse\" id=\"layout-";
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["Layout"], "id", [], "any", false, false, false, 74), "html", null, true);
                echo "\">
                                ";
                // line 75
                $context['_parent'] = $context;
                $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, $context["Layout"], "pages", [], "any", false, false, false, 75));
                foreach ($context['_seq'] as $context["_key"] => $context["Page"]) {
                    // line 76
                    echo "                                    <div class=\"card-body p-0\">
                                        <div class=\"row justify-content-between p-3\">
                                            <div class=\"col-auto\">
                                                <a href=\"";
                    // line 79
                    echo twig_escape_filter($this->env, $this->extensions['Eccube\Twig\Extension\IgnoreRoutingNotFoundExtension']->getUrl("admin_content_page_edit", ["id" => twig_get_attribute($this->env, $this->source, $context["Page"], "id", [], "any", false, false, false, 79)]), "html", null, true);
                    echo "\">";
                    echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["Page"], "name", [], "any", false, false, false, 79), "html", null, true);
                    echo "</a>
                                            </div>
                                        </div>
                                    </div>
                                ";
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_iterated'], $context['_key'], $context['Page'], $context['_parent'], $context['loop']);
                $context = array_intersect_key($context, $_parent) + $_parent;
                // line 84
                echo "                            </div>
                        ";
            } else {
                // line 86
                echo "                            <div class=\"collapse ec-cardCollapse\" id=\"layout-";
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["Layout"], "id", [], "any", false, false, false, 86), "html", null, true);
                echo "\">
                                <div class=\"p-0 empty-item\">
                                    <div class=\"d-block p-3\">
                                        <span class=\"text-muted\">";
                // line 89
                echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\TranslationExtension']->trans("admin.content.layout_no_page"), "html", null, true);
                echo "</span>
                                    </div>
                                </div>
                            </div>
                        ";
            }
            // line 94
            echo "                    </div>
                ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['Layout'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 96
        echo "
                <div class=\"modal fade\" id=\"DeleteModal\" tabindex=\"-1\" role=\"dialog\" aria-labelledby=\"DeleteModal\" aria-hidden=\"true\">
                    <div class=\"modal-dialog\" role=\"document\">
                        <div class=\"modal-content\">
                            <div class=\"modal-header\">
                                <h5 class=\"modal-title\">";
        // line 101
        echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\TranslationExtension']->trans("admin.common.delete_modal__title"), "html", null, true);
        echo "</h5>
                                <button class=\"close\" type=\"button\" data-dismiss=\"modal\" aria-label=\"Close\"><span aria-hidden=\"true\">×</span></button>
                            </div>
                            <div class=\"modal-body\">
                                <p class=\"modal-message\"><!-- jsでメッセージを挿入 --></p>
                            </div>
                            <div class=\"modal-footer\">
                                <button class=\"btn btn-ec-sub w-25\" type=\"button\" data-dismiss=\"modal\">";
        // line 108
        echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\TranslationExtension']->trans("admin.common.cancel"), "html", null, true);
        echo "</button>
                                <a href=\"#\" class=\"btn btn-ec-delete\" ";
        // line 109
        echo $this->extensions['Eccube\Twig\Extension\CsrfExtension']->getCsrfTokenForAnchor();
        echo " data-method=\"delete\" data-confirm=\"false\">
                                    ";
        // line 110
        echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\TranslationExtension']->trans("admin.common.delete"), "html", null, true);
        echo "
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
";
    }

    public function getTemplateName()
    {
        return "@admin/Content/layout_list.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  257 => 110,  253 => 109,  249 => 108,  239 => 101,  232 => 96,  225 => 94,  217 => 89,  210 => 86,  206 => 84,  193 => 79,  188 => 76,  184 => 75,  179 => 74,  176 => 73,  163 => 65,  157 => 62,  152 => 60,  148 => 59,  145 => 58,  143 => 57,  135 => 54,  129 => 52,  126 => 51,  123 => 50,  120 => 49,  117 => 48,  115 => 47,  109 => 43,  104 => 42,  97 => 39,  91 => 35,  87 => 34,  70 => 19,  66 => 18,  59 => 16,  52 => 15,  47 => 11,  45 => 13,  38 => 11,);
    }

    public function getSourceContext()
    {
        return new Source("", "@admin/Content/layout_list.twig", "C:\\xampp\\htdocs\\ec\\eccube-4.0.5\\src\\Eccube\\Resource\\template\\admin\\Content\\layout_list.twig");
    }
}
