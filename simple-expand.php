<?php

namespace jhse;

/*
Plugin Name: Simple Expand - Lightweight Read more toggle
Plugin URI: https://wordpress.com/plugins/simple-expand
Description: Add shortcode [expand] to add a read more toggle functionality, pure HTML/CSS.
Author: jonashjalmarsson
Version: 1.1
Author URI: https://jonashjalmarsson.se
*/

/*  Copyright 2022 Jonas Hjalmarsson (email: jonas@byjalma.se)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/



/**
 * helper class
 **/
class jhse_expand {
	private $style = '';
	private $first = true;
    public static function instance() {
        static $instance;
        if ($instance === null)
            $instance = new jhse_expand();
        return $instance;
    }
    private function __construct() { 
		$this->style = "<style>
	details.expand { border: 1px solid #ccc;border-radius: 4px;padding: 12px 12px 6px;margin-bottom: 24px }
	details.expand[open] { padding-bottom: 12px; }
	details.expand summary { margin-bottom:8px; font-weight:600; cursor:pointer; list-style-type: disclosure-closed; }
	details.expand[open] summary { list-style-type: disclosure-open; }
	
	details.expand[open] p:last-child { margin-bottom: 0px; }
	details.expand summary:first-of-type { display: list-item; counter-increment: list-item 0;  }

	details.expand summary::marker {
		unicode-bidi: isolate;
		font-variant-numeric: tabular-nums;
		text-transform: none;
		text-indent: 0px !important;
		text-align: start !important;
		text-align-last: start !important;
	}
	
	</style>";

	}
	private function getStyle() {
		if ($this->first) {
			$this->first = false;
			return $this->style;
		}
		return '';
	}

	public function render($atts, $content) {
		$title = \wp_kses($atts['title'],\wp_kses_allowed_html( 'post' ));
		add_filter( 'wp_kses_allowed_html', __NAMESPACE__ . '\allow_iframes', 1 );
		$content = \wp_kses($content, \wp_kses_allowed_html( 'post' ));
		remove_filter( 'wp_kses_allowed_html', __NAMESPACE__ . '\allow_iframes', 1 );
		$retValue = $this->getStyle();
		$retValue .= "<details class='expand'>";
		$retValue .= "<summary style='margin-bottom:6px; font-weight:600'>$title</summary>";
		$retValue .= do_shortcode($content);
		$retValue .= "</details>";
		return $retValue;
	}

}

function allow_iframes( $allowedposttags ){

	$allowedposttags['iframe'] = array(
		'align' => true,
		'allow' => true,
		'allowfullscreen' => true,
		'class' => true,
		'frameborder' => true,
		'height' => true,
		'id' => true,
		'marginheight' => true,
		'marginwidth' => true,
		'name' => true,
		'scrolling' => true,
		'src' => true,
		'style' => true,
		'width' => true,
		'allowFullScreen' => true,
		'class' => true,
		'frameborder' => true,
		'height' => true,
		'mozallowfullscreen' => true,
		'src' => true,
		'title' => true,
		'webkitAllowFullScreen' => true,
		'width' => true
	);

	return $allowedposttags;
}


/**
 * shortcode [expand]
 **/
function expand_func( $atts, $content = null ){
	$default = array(
		'title' => '',
		'class' => '',
		);
	$atts = shortcode_atts( $default, $atts );	
	$expand = jhse_expand::instance();
	return $expand->render($atts, $content);

}

add_shortcode( 'expand', __NAMESPACE__ . '\\expand_func' );



