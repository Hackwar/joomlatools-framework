<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Behavior Template Helper
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Component\Koowa\Template\Helper
 */
class KTemplateHelperUi extends KTemplateHelperAbstract
{
    /**
     * Loads the common UI libraries
     *
     * @param array $config
     * @return string
     */
    public function load($config = array())
    {
        $identifier = $this->getTemplate()->getIdentifier();

        $config = new KObjectConfigJson($config);
        $config->append(array(
            'debug' => false,
            'package' => $identifier->package,
            'domain'  => $identifier->domain,
            'styles' => array(),
            'wrapper_class' => array(
                'koowa-container koowa',
                $identifier->type.'_'.$identifier->package
            ),
        ))->append(array(
            'wrapper' => sprintf('<div class="%s">
                <!--[if lte IE 8 ]><div class="old-ie"><![endif]-->
                %%s
                <!--[if lte IE 8 ]></div><![endif]-->
                </div>', implode(' ', KObjectConfig::unbox($config->wrapper_class))
            )
        ));


        $html = '';

        if ($config->styles !== false)
        {
            if ($config->package) {
                $config->styles->package = $config->package;
            }

            if ($config->domain) {
                $config->styles->domain = $config->domain;
            }

            $config->styles->debug = $config->debug;

            $html .= $this->styles($config->styles);
        }

        $html .= $this->scripts($config);

        if ($config->wrapper) {
            $html .= $this->wrapper($config);
        }

        return $html;
    }

    public function styles($config = array())
    {
        $identifier = $this->getTemplate()->getIdentifier();

        $config = new KObjectConfigJson($config);
        $config->append(array(
            'debug' => false,
            'package' => $identifier->package,
            'domain'  => $identifier->domain
        ))->append(array(
            'folder' => 'com_'.$config->package,
            'file'   => ($identifier->type === 'mod' ? 'module' : $config->domain) ?: 'admin'
        ));

        $html = '';

        if (empty($config->css_file) && $config->css_file !== false) {
            $config->css_file = sprintf('%scss/%s.css', (empty($config->folder) ? '' : $config->folder.'/'), $config->file);
        }

        if ($config->css_file) {
            $html .= '<ktml:style src="assets://'.$config->css_file.'" />';
        }

        return $html;
    }

    public function scripts($config = array())
    {
        $identifier = $this->getTemplate()->getIdentifier();

        $config = new KObjectConfigJson($config);
        $config->append(array(
            'debug' => false,
            'domain'  => $identifier->domain
        ));

        $html = '';

        $html .= $this->getTemplate()->helper('behavior.modernizr', $config->toArray());

        if (($config->domain === 'admin' || $config->domain === '')  && !KTemplateHelperBehavior::isLoaded('admin.js')) {
            $html .= '<ktml:script src="assets://js/'.($config->debug ? 'build/' : 'min/').'admin.js" />';

            KTemplateHelperBehavior::setLoaded('admin.js');
            KTemplateHelperBehavior::setLoaded('modal');
            KTemplateHelperBehavior::setLoaded('select2');
            KTemplateHelperBehavior::setLoaded('tooltip');
            KTemplateHelperBehavior::setLoaded('tree');
            KTemplateHelperBehavior::setLoaded('calendar');
            KTemplateHelperBehavior::setLoaded('tooltip');
        }

        $html .= $this->getTemplate()->helper('behavior.koowa', $config->toArray());
        $html .= '<script data-inline type="text/javascript">(function() {var el = document.documentElement; var cl = "k-js-enabled"; if (el.classList) { el.classList.add(cl); }else{ el.className += " " + cl;}})()</script>';



        return $html;
    }


    public function wrapper($config = array())
    {
        $config = new KObjectConfigJson($config);

        $this->getTemplate()->addFilter('wrapper');
        $this->getTemplate()->getFilter('wrapper')->setWrapper($config->wrapper);

        return '<ktml:template:wrapper>'; // used to make sure the template only wraps once
    }
}