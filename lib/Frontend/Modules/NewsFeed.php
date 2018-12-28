<?php
namespace Froxlor\Frontend\Modules;

/**
 * This file is part of the Froxlor project.
 * Copyright (c) 2003-2009 the SysCP Team (see authors).
 * Copyright (c) 2010 the Froxlor Team (see authors).
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code. You can also view the
 * COPYING file online at http://files.froxlor.org/misc/COPYING.txt
 *
 * @copyright (c) the authors
 * @author Florian Lippert <flo@syscp.org> (2003-2009)
 * @author Froxlor team <team@froxlor.org> (2010-)
 * @license GPLv2 http://files.froxlor.org/misc/COPYING.txt
 * @package Panel
 *         
 */
use Froxlor\Frontend\FeModule;

class NewsFeed extends FeModule
{

	/**
	 * called via jquery/ajax to list latest news
	 */
	public function overview()
	{
		if (isset($_GET['role']) && $_GET['role'] == "customer") {
			$feed = \Froxlor\Settings::Get("customer.news_feed_url");
		} else {
			$feed = "https://inside.froxlor.org/news/";
		}

		if (function_exists("simplexml_load_file") == false) {
			echo \Froxlor\Frontend\UI::Twig()->render('newsfeed/warning.html.twig', array(
				'warning' => "Newsfeed not available due to missing php-simplexml extension.<br>Please install the php-simplexml extension in order to view our newsfeed."
			));
			return;
		}

		if (function_exists('curl_version')) {
			$output = \Froxlor\Http\HttpClient::urlGet($feed);
			$news = simplexml_load_string(trim($output));
		} else {
			echo \Froxlor\Frontend\UI::Twig()->render('newsfeed/warning.html.twig', array(
				'warning' => "Newsfeed not available due to missing php-curl extension.<br>Please install the php-curl extension in order to view our newsfeed."
			));
			return;
		}

		if ($news !== false) {
			for ($i = 0; $i < 3; $i ++) {
				$item = $news->channel->item[$i];

				$title = (string) $item->title;
				$link = (string) $item->link;
				$date = date("Y-m-d G:i", strtotime($item->pubDate));
				$content = preg_replace("/[\r\n]+/", " ", strip_tags($item->description));
				$content = substr($content, 0, 150) . "...";

				echo \Froxlor\Frontend\UI::Twig()->render('newsfeed/item.html.twig', array(
					'title' => $title,
					'link' => $link,
					'date' => $date,
					'content' => $content
				));
			}
		} else {
			echo "";
		}
	}
}
