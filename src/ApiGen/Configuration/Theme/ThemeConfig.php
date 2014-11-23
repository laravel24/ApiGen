<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Configuration\Theme;

use ApiGen\Configuration\ConfigurationException;
use ApiGen\Neon\NeonFile;
use Nette;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;


class ThemeConfig extends Nette\Object
{

	/**
	 * @var array
	 */
	private $options;

	/**
	 * @var array
	 */
	private $defaults = array(
		'name' => '',
		'options' => array(
			'elementDetailsCollapsed' => TRUE,
			'elementsOrder' => 'natural' # or: alphabetical
		),
		'resources' => array(
			'resources' => 'resources'
		),
		'templates' => array(
			'overview' => array(
				'filename' => 'index.html',
				'template' => 'overview.latte'
			),
			'combined' => array(
				'filename' => 'resources/combined.js',
				'template' => 'combined.js.latte'
			),
			'elementlist' => array(
				'filename' => 'elementlist.js',
				'template' => 'elementlist.js.latte'
			),
			'404' => array(
				'filename' => '404.html',
				'template' => '404.latte'
			),
			'package' => array(
				'filename' => 'package-%s.html',
				'template' => 'package.latte'
			),
			'namespace' => array(
				'filename' => 'namespace-%s.html',
				'template' => 'namespace.latte'
			),
			'class' => array(
				'filename' => 'class-%s.html',
				'template' => 'class.latte'
			),
			'constant' => array(
				'filename' => 'constant-%s.html',
				'template' => 'constant.latte'
			),
			'function' => array(
				'filename' => 'function-%s.html',
				'template' => 'function.latte'
			),
			'source' => array(
				'filename' => 'source-%s.html',
				'template' => 'source.latte'
			),
			'tree' => array(
				'filename' => 'tree.html',
				'template' => 'tree.latte'
			),
			'deprecated' => array(
				'filename' => 'deprecated.html',
				'template' => 'deprecated.latte'
			),
			'todo' => array(
				'filename' => 'todo.html',
				'template' => 'todo.latte'
			),
			'sitemap' => array(
				'filename' => 'sitemap.xml',
				'template' => 'sitemap.xml.latte'
			),
			'opensearch' => array(
				'filename' => 'opensearch.xml',
				'template' => 'opensearch.xml.latte'
			),
			'robots' => array(
				'filename' => 'robots.txt',
				'template' => 'robots.txt.latte'
			)
		)
	);

	/**
	 * @var string
	 */
	private $filePath;

	/**
	 * @var NeonFile
	 */
	private $file;


	/**
	 * @param string $filePath
	 */
	public function __construct($filePath)
	{
		$this->filePath = $filePath;
		if ( ! is_file($filePath)) {
			throw new ConfigurationException("File $filePath doesn't exist");
		}
		$this->file = new NeonFile($filePath);
	}



	/**
	 * @return array
	 */
	public function getOptions()
	{
		if ($this->options === NULL) {
			$this->options = $this->file->read();

			$resolver = new OptionsResolver;
			$resolver->setDefaults($this->getDefaults());

			$resolver->setAllowedValues(array(
				'templates' => function ($value) {
					foreach ($value as $type => $settings) {
						if ( ! is_file($settings['template'])) {
							throw new ConfigurationException("Template for $type was not found in "
								. $settings['template']);
						}
					}
					return TRUE;
				}
			));

			$resolver->setNormalizers(array(
				'templates' => function (Options $options, $value) {
					if ($this->isThemeUsed()) {
						foreach ($value as $type => $settings) {
							$value[$type]['template'] = $options['templatesPath'] . '/' . $settings['template'];
						}
					}
					return $value;
				}
			));

			$this->options = $resolver->resolve($this->options);
		}

		return $this->options;
	}


	/**
	 * @return array
	 */
	private function getDefaults()
	{
		return $this->defaults + array(
			'templatesPath' => dirname($this->filePath)
		);
	}


	/**
	 * @return bool
	 */
	private function isThemeUsed()
	{
		if ($this->filePath === APIGEN_ROOT_PATH . '/templates/default/config.neon') {
			return TRUE;

		} elseif ($this->filePath === APIGEN_ROOT_PATH . '/templates/bootstrap/config.neon') {
			return TRUE;
		}
		return FALSE;
	}

}
