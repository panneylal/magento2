<?php

	namespace Pl\Yd\Model\Rewrite\Order\Pdf;

	use Magento\Sales\Model\Order\Pdf\Invoice as CoreInvoice;

	class Invoice extends CoreInvoice
	{
		/**
		 * Set font as regular
		 *
		 * @param  \Zend_Pdf_Page $object
		 * @param  int $size
		 * @return \Zend_Pdf_Resource_Font
		 */
		protected function _setFontRegular($object, $size = 7)
		{
			$font = \Zend_Pdf_Font::fontWithPath(
				$this->_rootDirectory->getAbsolutePath('lib/internal/dejavu-sans/DejaVuSansCondensed.ttf')
			);
			$object->setFont($font, $size);
			return $font;
		}

		/**
		 * Set font as bold
		 *
		 * @param  \Zend_Pdf_Page $object
		 * @param  int $size
		 * @return \Zend_Pdf_Resource_Font
		 */
		protected function _setFontBold($object, $size = 7)
		{
			$font = \Zend_Pdf_Font::fontWithPath(
				$this->_rootDirectory->getAbsolutePath('lib/internal/dejavu-sans/DejaVuSansCondensed.ttf')
			);
			$object->setFont($font, $size);
			return $font;
		}

		/**
		 * Set font as italic
		 *
		 * @param  \Zend_Pdf_Page $object
		 * @param  int $size
		 * @return \Zend_Pdf_Resource_Font
		 */
		protected function _setFontItalic($object, $size = 7)
		{
			$font = \Zend_Pdf_Font::fontWithPath(
				$this->_rootDirectory->getAbsolutePath('lib/internal/dejavu-sans/DejaVuSansCondensed.ttf')
			);
			$object->setFont($font, $size);
			return $font;
		}


		protected function _drawFooter(\Zend_Pdf_Page $page)
		    {
		        /* Add table foot */
		        try {
		            $this->y -= 10;
		            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		            $fileSystem = $objectManager->create('\Magento\Framework\Filesystem');
		            $mediaPath = $fileSystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA)->getAbsolutePath();

		            $image = \Zend_Pdf_Image::imageWithPath($mediaPath.'wysiwyg\oanaked.png');   // Here $image is absolutepath of IMAGE
		            $top = 70;
		            //top border of the page
		            $widthLimit = 270;
		            //half of the page width
		            $heightLimit = 270;
		            
		            $width = $image->getPixelWidth();
		            $height = $image->getPixelHeight();

		            //preserving aspect ratio (proportions)
		            $ratio = $width / $height;
		            if ($ratio > 1 && $width > $widthLimit) {
		                $width = $widthLimit;
		                $height = $width / $ratio;
		            } elseif ($ratio < 1 && $height > $heightLimit) {
		                $height = $heightLimit;
		                $width = $height * $ratio;
		            } elseif ($ratio == 1 && $height > $heightLimit) {
		                $height = $heightLimit;
		                $width = $widthLimit;
		            }

		            $y1 = $top - $height;
		            $y2 = $top;
		            $x1 = 35;
		            $x2 = $x1 + $width;

		            //coordinates after transformation are rounded by Zend
		            $page->drawImage($image, $x1, $y1, $x2, $y2);
		            $font = $this->_setFontBoldOver($page, 10);
		            $value = $this->getFooterContent();
		            $line = 28;
		            if ($value !== '') {
		                $value = str_replace(' @ ', "\n", $value);

		                $page->setFillColor(new \Zend_Pdf_Color_RGB(0, 0, 0));
		                $page->setLineColor(new \Zend_Pdf_Color_GrayScale(0.5));
		                foreach(explode("\n", $value) as $textLine){
		                    //$feed = $this->getAlignCenter($textLine, 30, 520, $font, 12);
		                    $page->drawText(strip_tags($textLine), 120, $line, 'UTF-8');
		                    $line -=16;
		                }
		                $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0));
		            }
		            $this->y -= 20;
		        } catch (\Exception $e) {
		            \Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->info($e->getMessage());
		        }
		    }

	}