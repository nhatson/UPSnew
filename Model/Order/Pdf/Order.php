<?php
/**
 * Sales print UPS Labe / Retour
 *
 * @author  matze EuroGreens
 */
// require_once("EuroGreens/UpsLabelPrint/Model/Order/Pdf/paperpdf.php");
// require_once("EuroGreens/UpsLabelPrint/Model/Order/Pdf/html2pdf.php");
// require_once("EuroGreens/UpsLabelPrint/Model/Order/Pdf/fpdf.php");

//require_once("EuroGreens/UpsLabelPrint/Model/Order/HtmlFix/htmlfixer.php");

//require_once("EuroGreens/UpsLabelPrint/Model/Order/dompdf/dompdf_config.inc.php");
namespace Bss\Ups\Model\Order\Pdf;

use Magento\Sales\Model\Order\Pdf\Shipment;
use Magento\Sales\Model\Order\Pdf\Config;
use Magento\Framework\Filesystem\DirectoryList;
use Bss\Ups\Model\Order\Pdf\Paperpdf;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

class Order extends Shipment {

    /**
     * @param \Magento\Payment\Helper\Data $paymentData
     * @param \Magento\Framework\Stdlib\StringUtils $string
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Filesystem $filesystem
     * @param Config $pdfConfig
     * @param \Magento\Sales\Model\Order\Pdf\Total\Factory $pdfTotalFactory
     * @param \Magento\Sales\Model\Order\Pdf\ItemsFactory $pdfItemsFactory
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation
     * @param \Magento\Sales\Model\Order\Address\Renderer $addressRenderer
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Locale\ResolverInterface $localeResolver
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
	protected $label_path = "";
	protected $jpg_label_path = "";
	protected $logo_path = "";
	protected $item_data = array();
	protected $directory;
	protected $paperpdf;
	protected $upsData;
    protected $datetime;
    protected $timezone;


    public function __construct(
        \Magento\Payment\Helper\Data $paymentData,
        \Magento\Framework\Stdlib\StringUtils $string,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Filesystem $filesystem,
        Config $pdfConfig,
        \Magento\Sales\Model\Order\Pdf\Total\Factory $pdfTotalFactory,
        \Magento\Sales\Model\Order\Pdf\ItemsFactory $pdfItemsFactory,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Sales\Model\Order\Address\Renderer $addressRenderer,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Locale\ResolverInterface $localeResolver,
        DirectoryList $directory,
		Paperpdf $paperpdf,
		\Bss\Ups\Model\ResourceModel\UpsData $upsData,
        DateTime $datetime,
        TimezoneInterface $timezone,		
        array $data = []
    ) {
        parent::__construct(
            $paymentData,
            $string,
            $scopeConfig,
            $filesystem,
            $pdfConfig,
            $pdfTotalFactory,
            $pdfItemsFactory,
            $localeDate,
            $inlineTranslation,
            $addressRenderer,
            $storeManager,
            $localeResolver,
            $data
        );
        $this->directory = $directory;
        $this->paperpdf = $paperpdf;
        $this->upsData = $upsData;
        $this->datetime  = $datetime;
        $this->timezone = $timezone;        
        $this->setVar();
    }

	public function setVar()
	{
		$this->label_path = $this->directory->getPath('app') .DIRECTORY_SEPARATOR.'code'.DIRECTORY_SEPARATOR.'Bss'.DIRECTORY_SEPARATOR.'Ups'.DIRECTORY_SEPARATOR.'ups'.DIRECTORY_SEPARATOR.'upsLabels';
		$this->jpg_label_path = $this->directory->getPath('app') .DIRECTORY_SEPARATOR.'code'.DIRECTORY_SEPARATOR.'Bss'.DIRECTORY_SEPARATOR.'Ups'.DIRECTORY_SEPARATOR.'ups'.DIRECTORY_SEPARATOR.'ups_jpgLabels';
		$this->logo_path = '';
	
	}

	// Paketlabel PDF
	public function getPdff($orders = array()) {
		$format=array(152,102);
		$pdf = $this->paperpdf;
		$pdf->__PDFHTML('L','mm',$format);


		// $connection = Mage::getSingleton('core/resource')->getConnection('core_read');
		// $select_ups = $connection->select("SELECT *")->from('ups_data')->where("`order_id`='".$orders[1]."' AND `retour`='nein'")->order("");
		$if_ups_data = $this->upsData->checkExist($orders[1], 'nein');
		$html = '';

		foreach($if_ups_data as $ud) {

			$b_cod = $ud['nach'];

			$package = explode(";", $ud['paketnummer']);
			$counter = 0;

			foreach($package as $p){

				$imageFile = $this->label_path. '/'.$p.".gif";

				// is available?
				if (file_exists($imageFile)) {		
					//new page
					$pdf->AddPage();
					$pdf->SetFont('Arial','B',9);
					$pdf->SetX(10);
					$pdf->SetY(5);
					//152x102 mm
					//Image($file, $x=null, $y=null, $w=0, $h=0, $type='', $link='')
					$pdf->Image($this->convert_image($imageFile,$p), 0, 0, 152, 102);
					$pdf->IncludeJS("print('true');");
					
				}
				$counter++;
			}
		}
		$pdf->Output($orders[1].'.pdf', 'I');
		return true;

	}	
	
	public function getPdfRetour($orders = array())
	{
		$pdf = $this->paperpdf;
		$pdf->__PDFHTML();
		$if_ups_data = $this->upsData->checkExist($orders[1], 'ja');
	
		foreach($if_ups_data as $ud){
	
			$package = explode(";", $ud['paketnummer']);
			$counter = 0;
	
			foreach($package as $p){
	
				$imageFile = $this->label_path. '/'.$p.".gif";
	
				// is available?
				if (file_exists($imageFile)){
					//die('xxxx');
					//new page
					$pdf->AddPage();
					$pdf->SetFont('Arial','B',9);	
					$pdf->SetX(10);
					$pdf->SetY(5);
					$pdf->Image($this->convert_image($imageFile,$p), 10, 5, 230, 138);
					$pdf->IncludeJS("print('true');");
	
				}
	
				$counter++;
			}
	
		}
        $now = $this->datetime->gmtDate();
        $date = $this->timezone->date($now)->format('Y-m-d_H-i-s');
		$pdf->Output('Retourscheine'.$date.'.pdf', 'I');
		return true;
	
	}
	
	//add cod frame to ups label
	protected function get_cod_frame($pdfFrame){
		
		$pdfFrame->SetY(150);
		$pdfFrame->Ln(10);
		$pdfFrame->SetFont('Arial','B',9);
		$pdfFrame->Cell(40, 15, 'Drivers Signature', 1, '', 'C', 0, '');
		$pdfFrame->Cell(30, 15, 'COD Amount', 1, '', 'C', 0, '');
		$pdfFrame->Cell(30, 15, 'Currency Code', 1, '', 'C', 0, '');
		$pdfFrame->Cell(40, 15, 'COD Collection Type', 1,'' , 'C', 0, '');
		$pdfFrame->Cell(30, 15, 'Date', 1, '', 'C', 0, '');
		
		$pdfFrame->SetY(175);
		$pdfFrame->SetX(10);
		$pdfFrame->Cell(40, 15, '', 1, '', 'C', 0, '');
		$pdfFrame->Cell(30, 15, '', 1, '', 'C', 0, '');
		$pdfFrame->Cell(30, 15, '', 1, '', 'C', 0, '');
		$pdfFrame->Cell(40, 15, '', 1,'' , 'C', 0, '');
		$pdfFrame->Cell(30, 15, '', 1, '', 'C', 0, '');
		
		return $pdfFrame;
		
	}
	
	//create jpg
	protected function convert_image($if,$tr){
		
		$im = @imagecreatefromgif ($if);
		$im_path  = $this->jpg_label_path."/label".$tr.".jpg";
		imagejpeg($im,$im_path,80);
		imagedestroy($im);
		
		return $im_path;
		
	}
	
	
}
