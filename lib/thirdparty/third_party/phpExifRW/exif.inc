<?php
/**
 * PHP Class to read, write and transfer EXIF information
 * that most of the digital camera produces.
 *
 * This class is based on jhead (in C) by Matthias Wandel
 *
 * Vinay Yadav (vinayRas) < vinay@sanisoft.com >
 * http://www.sanisoft.com/phpexifrw/
 *
 * For more information on EXIF
 * http://www.pima.net/standards/it10/PIMA15740/exif.htm
 *
 *
 *
 *
 * Features:
 *   - Read Exif Information
 *   - Extract and display emdedded thumbnails
 *   - Transfer Exif Information
 *   - Add Comments to files.
 *   - Tranfering EXIF information from one file to another.
 *
 *   TODO
 *       1. Writing exif information to the file.
 *       2. Add EXIF audio reading methods (I think it exists!)
 *       3. Support of additional tags.
 *       4. Handling Unicode character in UserComment tag of EXif Information.
 */
/** * Start Of Frame N */
define("M_SOF0",0xC0);
/** * N indicates which compression process */
define("M_SOF1",0xC1);
/** * Only SOF0-SOF2 are now in common use */
define("M_SOF2",0xC2);
/** *  */
define("M_SOF3",0xC3);
/** * NB: codes C4 and CC are NOT SOF markers */
define("M_SOF5",0xC5);
/** *  */
define("M_SOF6",0xC6);
/** *  */
define("M_SOF7",0xC7);
/** *  */
define("M_SOF9",0xC9);
/** *  */
define("M_SOF10",0xCA);
/** *  */
define("M_SOF11",0xCB);
/** *  */
define("M_SOF13",0xCD);
/** *  */
define("M_SOF14",0xCE);
/** *  */
define("M_SOF15",0xCF);
/** * Start Of Image (beginning of datastream) */
define("M_SOI",0xD8);
/** * End Of Image (end of datastream) */
define("M_EOI",0xD9);
/** * Start Of Scan (begins compressed data) */
define("M_SOS",0xDA);
/** * Jfif marker */
define("M_JFIF",0xE0);
/** * Exif marker */
define("M_EXIF",0xE1);
/** * Image Title */
define("M_COM",0xFE);
define("NUM_FORMATS","12");
/** * Tag Data Format */
define("FMT_BYTE","1");
/** * ASCII */
define("FMT_STRING","2");
/** * Short */
define("FMT_USHORT","3");
/** * Long */
define("FMT_ULONG","4");
/** * Rational */
define("FMT_URATIONAL","5");
/** * Byte */
define("FMT_SBYTE","6");
/** * Undefined */
define("FMT_UNDEFINED","7");
/** * Short */
define("FMT_SSHORT","8");
/** * Long */
define("FMT_SLONG","9");
/** * Rational */
define("FMT_SRATIONAL","10");
/** * Single */
define("FMT_SINGLE","11");
/** * Double */
define("FMT_DOUBLE","12");
/** * Exif IFD */
define("TAG_EXIF_OFFSET","0x8769");
/** * Interoperability tag */
define("TAG_INTEROP_OFFSET","0xa005");
/** * Image input equipment manufacturer */
define("TAG_MAKE","0x010F");
/** * Image input equipment model */
define("TAG_MODEL","0x0110");
/** * Orientation of image */
define("TAG_ORIENTATION","0x0112");
/** * Exposure Time */
define("TAG_EXPOSURETIME","0x829A");
/** * F Number */
define("TAG_FNUMBER","0x829D");
/** * Shutter Speed */
define("TAG_SHUTTERSPEED","0x9201");
/** * Aperture */
define("TAG_APERTURE","0x9202");
/** * Aperture */
define("TAG_MAXAPERTURE","0x9205");
/** * Lens Focal Length */
define("TAG_FOCALLENGTH","0x920A");
/** * The date and time when the original image data was generated. */
define("TAG_DATETIME_ORIGINAL","0x9003");
/** * User Comments */
define("TAG_USERCOMMENT","0x9286");
/** * subject Location */
define("TAG_SUBJECT_DISTANCE","0x9206");
/** * Flash */
define("TAG_FLASH","0x9209");
/** * Focal Plane X Resolution */
define("TAG_FOCALPLANEXRES","0xa20E");
/** * Focal Plane Resolution Units */
define("TAG_FOCALPLANEUNITS","0xa210");
/** * Image Width */
define("TAG_EXIF_IMAGEWIDTH","0xA002");
/** * Image Height */
define("TAG_EXIF_IMAGELENGTH","0xA003");
/** * Exposure Bias */
define("TAG_EXPOSURE_BIAS","0x9204");
/** * Light Source */
define("TAG_WHITEBALANCE","0x9208");
/** * Metering Mode */
define("TAG_METERING_MODE","0x9207");
/** * Exposure Program */
define("TAG_EXPOSURE_PROGRAM","0x8822");
/** * ISO Equivalent Speed Rating */
define("TAG_ISO_EQUIVALENT","0x8827");
/** * Compressed Bits Per Pixel */
define("TAG_COMPRESSION_LEVEL","0x9102");
/** * Thumbnail Start Offset */
define("TAG_THUMBNAIL_OFFSET","0x0201");
/** * Thumbnail Length */
define("TAG_THUMBNAIL_LENGTH","0x0202");
/** * Image Marker */
define("PSEUDO_IMAGE_MARKER",0x123);
/** * Max Image Title Length */
define("MAX_COMMENT",2000);
define("TAG_ARTIST","0x013B");
define("TAG_COPYRIGHT","0x8298");
/**
 * As more and more tags will be added will, the contents of array will increase.
 * DONT remove any blank array, since they do contain several tags.
*/
 $FMT_BYTE_ARRAY = array();
 $FMT_STRING_ARRAY = array(
            0x010E,  //Image title
            0x010F, // Make - Image input equipment manufacturer
            0x0110, // Model - Image input equipment model
            0x0131, // Software - Software used
            0x013B, // Artist - Person who created the image
            0x8298,// Copyright - Copyright holder
            0x9003, // DateTimeOriginal - Date and time of original data generation
            );
 $FMT_USHORT_ARRAY = array(
            0x0112, // Orientation
            0x8822, // Exposure Program
            0x9207, // Metering mode
            0x9209, // Flash
            0xA002, // Valid image width      PixelXDimension
            0xA003, // Valid image height      PixelYDimension
            );
 $FMT_ULONG_ARRAY = array(
            0x0202, // JPEGInterchangeFormatLength
            );
 $FMT_URATIONAL_ARRAY = array(
                0x829A, // Exposure Time
                0x829D, // F Number
                0x9102, // CompressedBitsPerPixel
                0x9202, // Aperture
                0x9205, // MaxApertureValue
                0x920A, // focal length
                );
 $FMT_SBYTE_ARRAY = array();
 $FMT_UNDEFINED_ARRAY = array();
 $FMT_SSHORT_ARRAY = array();
 $FMT_SLONG_ARRAY = array();
 $FMT_SRATIONAL_ARRAY = array(
                0x9201, // shutter speed
                0x9204, // Exposure Bias
            );
 $FMT_SINGLE_ARRAY = array();
 $FMT_DOUBLE_ARRAY = array();
/** error Description  */
/**
  1 - File does not exists!
  2 -
  3 - Filename not provided
  10 - too many padding bytes
  11 - "invalid marker"
  12 - Premature end of file?
  51 - "Illegal subdirectory link"
  52 - "NOT EXIF FORMAT"
  53 - "Invalid Exif alignment marker.\n"
  54 - "Invalid Exif start (1)"
*/
$TagTable  = array(
  array(   0x100,   "ImageWidth"),
  array(   0x101,   "ImageLength"),
  array(   0x102,   "BitsPerSample"),
  array(   0x103,   "Compression"),
  array(   0x106,   "PhotometricInterpretation"),
  array(   0x10A,   "FillOrder"),
  array(   0x10D,   "DocumentName"),
  array(   0x10E,   "ImageDescription"),
  array(   0x10F,   "Make"),
  array(   0x110,   "Model"),
  array(   0x111,   "StripOffsets"),
  array(   0x112,   "Orientation"),
  array(   0x115,   "SamplesPerPixel"),
  array(   0x116,   "RowsPerStrip"),
  array(   0x117,   "StripByteCounts"),
  array(   0x11A,   "XResolution"),
  array(   0x11B,   "YResolution"),
  array(   0x11C,   "PlanarConfiguration"),
  array(   0x128,   "ResolutionUnit"),
  array(   0x12D,   "TransferFunction"),
  array(   0x131,   "Software"),
  array(   0x132,   "DateTime"),
  array(   0x13B,   "Artist"),
  array(   0x13E,   "WhitePoint"),
  array(   0x13F,   "PrimaryChromaticities"),
  array(   0x156,   "TransferRange"),
  array(   0x200,   "JPEGProc"),
  array(   0x201,   "ThumbnailOffset"),
  array(   0x202,   "ThumbnailLength"),
  array(   0x211,   "YCbCrCoefficients"),
  array(   0x212,   "YCbCrSubSampling"),
  array(   0x213,   "YCbCrPositioning"),
  array(   0x214,   "ReferenceBlackWhite"),
  array(   0x828D,  "CFARepeatPatternDim"),
  array(   0x828E,  "CFAPattern"),
  array(   0x828F,  "BatteryLevel"),
  array(   0x8298,  "Copyright"),
  array(   0x829A,  "ExposureTime"),
  array(   0x829D,  "FNumber"),
  array(   0x83BB,  "IPTC/NAA"),
  array(   0x8769,  "ExifOffset"),
  array(   0x8773,  "InterColorProfile"),
  array(   0x8822,  "ExposureProgram"),
  array(   0x8824,  "SpectralSensitivity"),
  array(   0x8825,  "GPSInfo"),
  array(   0x8827,  "ISOSpeedRatings"),
  array(   0x8828,  "OECF"),
  array(   0x9000,  "ExifVersion"),
  array(   0x9003,  "DateTimeOriginal"),
  array(   0x9004,  "DateTimeDigitized"),
  array(   0x9101,  "ComponentsConfiguration"),
  array(   0x9102,  "CompressedBitsPerPixel"),
  array(   0x9201,  "ShutterSpeedValue"),
  array(   0x9202,  "ApertureValue"),
  array(   0x9203,  "BrightnessValue"),
  array(   0x9204,  "ExposureBiasValue"),
  array(   0x9205,  "MaxApertureValue"),
  array(   0x9206,  "SubjectDistance"),
  array(   0x9207,  "MeteringMode"),
  array(   0x9208,  "LightSource"),
  array(   0x9209,  "Flash"),
  array(   0x920A,  "FocalLength"),
  array(   0x927C,  "MakerNote"),
  array(   0x9286,  "UserComment"),
  array(   0x9290,  "SubSecTime"),
  array(   0x9291,  "SubSecTimeOriginal"),
  array(   0x9292,  "SubSecTimeDigitized"),
  array(   0xA000,  "FlashPixVersion"),
  array(   0xA001,  "ColorSpace"),
  array(   0xA002,  "ExifImageWidth"),
  array(   0xA003,  "ExifImageLength"),
  array(   0xA005,  "InteroperabilityOffset"),
  array(   0xA20B,  "FlashEnergy"),                 // 0x920B in TIFF/EP
  array(   0xA20C,  "SpatialFrequencyResponse"),  // 0x920C    -  -
  array(   0xA20E,  "FocalPlaneXResolution"),     // 0x920E    -  -
  array(   0xA20F,  "FocalPlaneYResolution"),      // 0x920F    -  -
  array(   0xA210,  "FocalPlaneResolutionUnit"),  // 0x9210    -  -
  array(   0xA214,  "SubjectLocation"),             // 0x9214    -  -
  array(   0xA215,  "ExposureIndex"),            // 0x9215    -  -
  array(   0xA217,  "SensingMethod"),            // 0x9217    -  -
  array(   0xA300,  "FileSource"),
  array(   0xA301,  "SceneType"),
  array(      0, NULL)
 ) ;
$ProcessTable = array(
    array(M_SOF0,   "Baseline"),
    array(M_SOF1,   "Extended sequential"),
    array(M_SOF2,   "Progressive"),
    array(M_SOF3,   "Lossless"),
    array(M_SOF5,   "Differential sequential"),
    array(M_SOF6,   "Differential progressive"),
    array(M_SOF7,   "Differential lossless"),
    array(M_SOF9,   "Extended sequential, arithmetic coding"),
    array(M_SOF10,  "Progressive, arithmetic coding"),
    array(M_SOF11,  "Lossless, arithmetic coding"),
    array(M_SOF13,  "Differential sequential, arithmetic coding"),
    array(M_SOF14,  "Differential progressive, arithmetic coding"),
    array(M_SOF15,  "Differential lossless, arithmetic coding"),
    array(0,        "Unknown")
);
/**
 * PHP Class to read, write and transfer EXIF information
 * that most of the digital camera produces
 * Currenty it can only read JPEG file.
 */
 /**
 * @author Vinay Yadav (vinayRas) < vinay@sanisoft.com >
 *
 * @todo Writing exif information to the file.
 * @todo Add EXIF audio reading methods (I think it exists!)
 * @todo Support of additional tags.
 * @todo Handling Unicode character in UserComment tag of EXif Information.
 *
 * @version 0.5
 * @licence http://opensource.org/licenses/lgpl-license.php GNU LGPL
 */
class phpExifRW {
    /***
    * Array containg all Exif and JPEG image attributes
    * into regular expressions for themselves.
    * $ImageInfo[TAG] = TAG_VALUE;
    *
    * @var       array
    * @access    private
    *
    */
    var $ImageInfo = array();
    var $MotorolaOrder = 0;
    var $ExifImageWidth = 0; //
    var $FocalplaneXRes = 0; //
    var $FocalplaneUnits = 0; //
    var $sections = array();
    var $currSection = 0;  /** Stores total number fo Sections */
    var $BytesPerFormat = array(0,1,1,2,4,8,1,1,2,4,8,4,8);
    var $DirWithThumbnailPtrs = 0;
    var $ThumbnailSize = 0;
    var $ReadMode = array(
                            "READ_EXIF" => 1,
                            "READ_IMAGE" => 2,
                            "READ_ALL" => 3
                        );
    var $ImageReadMode = 3; /** related to $RealMode arrays values */
    var $file =  "";     /** JPEG file to parse for EXIF data */
    var $newFile = 1;   /** flag to check if the current file has been parsed or not. */
    var $thumbnail = ""; /* Name of thumbnail */
    var $thumbnailURL = ""; /* */
    var $exifSection = -1;   // market the exif section index oout of all sections
    var $errno = 0;
    var $errstr = "";
    var $debug = false;
    var $showTags = false;
    // Caching ralated variables
    var $caching = true; /* Should cacheing of image thumnails be allowed? */
    var $cacheDir = ""; /* Checkout constructor for default path. */
    /**
     * Constructor
     * @param string File name to be parsed.
     *
     */
    function phpExifRW($file = "") {
      if(!empty($file)) {
        $this->file = $file;
      }
      /**
      * Initialize some variables. Avoid lots of errors with fulll error_reporting
      */
      $this->ExifImageLength       = 0;
      $this->ImageInfo["CCDWidth"] = 0;
      $this->ImageInfo["Distance"] = 0;
      $this->ImageInfo[M_COM]      = "";
      $this->ImageInfo[TAG_FLASH]  = 0;
      $this->ImageInfo[TAG_MAXAPERTURE] = 0;
      /** check if file exists! */
      if(!file_exists($this->file)) {
         $this->errno = 1;
         $this->errstr = "File '".$this->file."' does not exists!";
      }
      $this->currSection = 0;
    }
    /**
     * Show Debugging information
     *
     * @param   string     Debugging message to display
     * @param   int   Type of error (0 - Warning, 1 - Error)
     * @return    void
     *
     */
    function debug($str,$TYPE = 0) {
       if($this->debug) {
        echo "<br>$str";
        if($TYPE == 1) {
           exit;
        }
       }
    }
    /**
     * Processes the whole file.
     *
     */
    function processFile() {
        /** dont reparse the whole file. */
        if(!$this->newFile) return true;
        $i = 0; $exitAll = 0;
        /** Open the JPEG in binary safe reading mode */
        $fp = fopen($this->file,"rb");
        $this->ImageInfo["FileName"] = $this->file;
        $this->ImageInfo["FileSize"] = filesize($this->file); /** Size of the File */
        $this->ImageInfo["FileDateTime"] = filectime($this->file); /** File node change time */
        /** check whether jped image or not */
        $a = fgetc($fp);
        if (ord($a) != 0xff || ord(fgetc($fp)) != M_SOI){
                $this->debug("Not a JPEG FILE",1);
                $this->errorno = 1;
                $this->errorstr = "File '".$this->file."' does not exists!";
        }
        $tmpTestLevel = 0;
        /** Examines each byte one-by-one */

        while(!feof($fp)) {
            $data = array();
                for ($a=0;$a<7;$a++){
                        $marker = fgetc($fp);
                        if (ord($marker) != 0xff) break;
                        if ($a >= 6){
                                $this->errno = 10;
                                $this->errstr = "too many padding bytes!";
                                $this->debug($this->errstr,1);
                                return false;
                        }
                }
                if (ord($marker) == 0xff){
                    // 0xff is legal padding, but if we get that many, something's wrong.
                    $this->errno = 10;
                    $this->errstr = "too many padding bytes!";
                    $this->debug($this->errstr,1);
                }
        $marker = ord($marker);
        $this->sections[$this->currSection]["type"] = $marker;
        // Read the length of the section.
        $lh = ord(fgetc($fp));
        $ll = ord(fgetc($fp));
        $itemlen = ($lh << 8) | $ll;
        if ($itemlen < 2){
                $this->errno = 11;
                $this->errstr = "invalid marker";
                $this->debug($this->errstr,1);
        }
        $this->sections[$this->currSection]["size"] = $itemlen;
        $tmpDataArr = array();  /** Temporary Array */
        if ($itemlen > 1)
          $tmpStr = fread($fp,$itemlen-2);
        else
          $tmpStr = '';
        $tmpDataArr[] = chr($lh);
        $tmpDataArr[] = chr($ll);
        $chars = preg_split('//', $tmpStr, -1, PREG_SPLIT_NO_EMPTY);
        $tmpDataArr = array_merge($tmpDataArr,$chars);
        $data = $tmpDataArr;
        $this->sections[$this->currSection]["data"] = $data;
        $this->debug("<hr><h1>".$this->currSection.":</h1>");
        //print_r($data);
        $this->debug("<hr>");
        if(count($data) != $itemlen) {
            $this->errno = 12;
            $this->errstr = "Premature end of file?";
            $this->debug($this->errstr,1);
        }
        $this->currSection++; /** */
        switch($marker) {
                case M_EXIF:
                        // Seen files from some 'U-lead' software with Vivitar scanner
                        // that uses marker 31 for non exif stuff.  Thus make sure
                        // it says 'Exif' in the section before treating it as exif.
                        $this->debug("<br>Found '".M_EXIF."'(Exif) Section, Proccessing<br>");
                        $this->exifSection = $this->currSection-1;
                        if (($this->ImageReadMode & $this->ReadMode["READ_EXIF"]) && ($data[2].$data[3].$data[4].$data[5]) == "Exif"){
                                $this->process_EXIF($data, $itemlen);
                                fclose($fp);
                                return;

                        }else{
                                // Discard this section.
                                $this->sections[--$this->currSection]["data"] = "";
                        }
                        $this->debug("<br>'".M_EXIF."'(Exif) Section, PROCESSED<br>");
                        $tmpTestLevel++;
                break;
        }
        $i++;
        if($exitAll == 1)  break;
        if($tmpTestLevel == 2)  break;
        }
        fclose($fp);
        $this->newFile = 0;
    }
    /**
     * Process one of the nested EXIF directories.
     * @param   string        All directory information
     * @param   string     whole Section
     * @param   int  Length of exif section
     *
    */
    function ProcessExifDir($DirStart, $OffsetBase, $ExifLength) {
        global $TagTable;
        $NumDirEntries = 0;
        $ValuePtr = array();
        $NumDirEntries = $this->Get16u($DirStart[0],$DirStart[1]);
        $this->debug("<br>Directory with $NumDirEntries entries\n");
        for ($de=0;$de<$NumDirEntries;$de++){
            $DirEntry = array_slice($DirStart,2+12*$de);
            $Tag = $this->Get16u($DirEntry[0],$DirEntry[1]);
            if (($Tag == TAG_DATETIME_ORIGINAL)
                || ($Tag == TAG_EXIF_OFFSET)
                || ($Tag == TAG_INTEROP_OFFSET))
            {
              $Format = $this->Get16u($DirEntry[2],$DirEntry[3]);
              $Components = $this->Get32u($DirEntry[4],$DirEntry[5],$DirEntry[6],$DirEntry[7]);
              /**
              if ((Format-1) >= NUM_FORMATS) {
                  // (-1) catches illegal zero case as unsigned underflows to positive large.
                  ErrNonfatal("Illegal number format %d for tag %04x", Format, Tag);
                  continue;
              }
              */
              $ByteCount = $Components * $this->BytesPerFormat[$Format];
              if ($ByteCount > 4){
                  $OffsetVal = $this->Get32u($DirEntry[8],$DirEntry[9],$DirEntry[10],$DirEntry[11]);
                  if ($OffsetVal+$ByteCount > $ExifLength){
                      $this->debug("Illegal value pointer($OffsetVal) for tag $Tag",1);
                  }
                  $ValuePtr = array_slice($OffsetBase,$OffsetVal);
              } else {
                  $ValuePtr = array_slice($DirEntry,8);
              }
              // Extract useful components of tag
              switch($Tag){
                  case TAG_DATETIME_ORIGINAL:
                      $this->ImageInfo[TAG_DATETIME_ORIGINAL] =  implode("",array_slice($ValuePtr,0,$ByteCount));
                      $this->ImageInfo["DateTime"]  = implode("",array_slice($ValuePtr,0));
                      return;
                  case TAG_EXIF_OFFSET:
                  case TAG_INTEROP_OFFSET:
                      {
                          $SubdirStart = array_slice($OffsetBase,$this->Get32u($ValuePtr[0],$ValuePtr[1],$ValuePtr[2],$ValuePtr[3]));
                          //if ($SubdirStart < $OffsetBase || $SubdirStart > $OffsetBase+$ExifLength){
                          //    debug("Illegal exif or interop ofset directory link",1);
                          //}else{
                              $this->ProcessExifDir($SubdirStart, $OffsetBase, $ExifLength);
                          //}
                          return;
                      }
              }
          }
        }
    }
    /**
     * Process Exif data
     * @param   array    Section data as an array
     * @param   int  Length of the section (length of data array)
     *
     */
    function process_EXIF($data,$length) {
        $this->debug("Exif header $length bytes long\n");
        if(($data[2].$data[3].$data[4].$data[5]) != "Exif") {
            $this->errno = 52;
            $this->errstr = "NOT EXIF FORMAT";
            $this->debug($this->errstr,1);
        }
        $this->ImageInfo["FlashUsed"] = 0;
            /** If it s from a digicam, and it used flash, it says so. */
        $this->FocalplaneXRes = 0;
        $this->FocalplaneUnits = 0;
        $this->ExifImageWidth = 0;
        if(($data[8].$data[9]) == "II") {
            $this->debug("Exif section in Intel order\n");
            $this->MotorolaOrder = 0;
        } else if(($data[8].$data[9]) == "MM") {
            $this->debug("Exif section in Motorola order\n");
            $this->MotorolaOrder = 1;
        } else {
            $this->errno = 53;
            $this->errstr = "Invalid Exif alignment marker.\n";
            $this->debug($this->errstr,1);
            return;
        }
        if($this->Get16u($data[10],$data[11]) != 0x2A || $this->Get32s($data[12],$data[13],$data[14],$data[15]) != 0x08) {
            $this->errno = 54;
            $this->errstr = "Invalid Exif start (1)";
            $this->debug($this->errstr,1);
        }
        $DirWithThumbnailPtrs = NULL;
        $this->ProcessExifDir(array_slice($data,16),array_slice($data,8),$length);
        // Compute the CCD width, in milimeters.                      2
        if ($this->FocalplaneXRes != 0){
            $this->ImageInfo["CCDWidth"] = (float)($this->ExifImageWidth * $this->FocalplaneUnits / $this->FocalplaneXRes);
        }
        $this->debug("Non settings part of Exif header: ".$length." bytes\n");
    } // end of function process_EXIF
    /**
     * Converts two byte number into its equivalent int integer
     * @param   int
     * @param   int
     *
     */
    function Get16u($val,$by) {
        if($this->MotorolaOrder){
            return ((ord($val) << 8) | ord($by));
        } else {
            return ((ord($by) << 8) | ord($val));
        }
    }
    /**
     * Converts 4-byte number into its equivalent integer
     *
     * @param   int
     * @param   int
     * @param   int
     * @param   int
     *
     * @return int
     */
    function Get32s($val1,$val2,$val3,$val4)
    {
        $val1 = ord($val1);
        $val2 = ord($val2);
        $val3 = ord($val3);
        $val4 = ord($val4);
        if ($this->MotorolaOrder){
            return (($val1 << 24) | ($val2 << 16) | ($val3 << 8 ) | ($val4 << 0 ));
        }else{
            return  (($val4 << 24) | ($val3 << 16) | ($val2 << 8 ) | ($val1 << 0 ));
        }
    }
    /**
     * Converts 4-byte number into its equivalent integer with the help of Get32s
     *
     * @param   int
     * @param   int
     * @param   int
     * @param   int
     *
     * @return int
     *
     */
    function get32u($val1,$val2,$val3,$val4) {
        return ($this->Get32s($val1,$val2,$val3,$val4) & 0xffffffff);
    }
} // end of class
?>
