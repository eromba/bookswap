<?php

/**
 * Random User Agent Generator
 * Based on random-uagent by Mike White:
 * https://github.com/mwhite/random-uagent.git
 */
class Random_UA {

  /**
   * Sources:
   * http://en.wikipedia.org/wiki/Usage_share_of_web_browsers#Summary_table
   * http://statowl.com/operating_system_market_share_by_os_version.php
   */
  protected function chooseRandomBrowserAndOS() {
      $frequencies = array(
          34 => array(
              89 => array('chrome', 'win'),
              9 => array('chrome', 'mac'),
              2 => array('chrome', 'lin')
          ),

          32 => array(
              100 => array('iexplorer', 'win')
          ),

          25 => array(
              83 => array('firefox', 'win'),
              16 => array('firefox', 'mac'),
              1 => array('firefox', 'lin')
          ),

          7 => array(
              95 => array('safari', 'mac'),
              4 => array('safari', 'win'),
              1 => array('safari', 'lin')
          ),

          2 => array(
              91 => array('opera', 'win'),
              6 => array('opera', 'lin'),
              3 => array('opera', 'mac')
          )
      );

      $rand = rand(1, 100);
      $sum = 0;
      foreach ($frequencies as $freq => $osFreqs) {
          $sum += $freq;
          if ($rand <= $sum) {
              $rand = rand(1, 100);
              $sum = 0;
              foreach ($osFreqs as $freq => $choice) {
                  $sum += $freq;
                  if ($rand <= $sum) {
                      return $choice;
                  }
              }
          }
      }

      throw new Exception("Frequencies don't sum to 100.");
  }

  protected function array_random(array $array) {
      return $array[array_rand($array, 1)];
  }

  protected function nt_version() {
      return rand(5, 6) . '.' . rand(0, 1);
  }

  protected function ie_version() {
      return rand(7, 9) . '.0';
  }

  protected function trident_version() {
      return rand(3, 5) . '.' . rand(0, 1);
  }

  protected function osx_version() {
      return "10_" . rand(5, 7) . '_' . rand(0, 9);
  }

  protected function chrome_version() {
      return rand(13, 15) . '.0.' . rand(800, 899) . '.0';
  }

  protected function presto_version() {
      return '2.9.' . rand(160, 190);
  }

  protected function presto_version2() {
      return rand(10, 12) . '.00';
  }

  protected function firefox($arch) {
      $ver = self::array_random(array(
        'Gecko/' . date('Ymd', rand(strtotime('2011-1-1'), time())) . ' Firefox/' . rand(5, 7) . '.0',
        'Gecko/' . date('Ymd', rand(strtotime('2011-1-1'), time())) . ' Firefox/' . rand(5, 7) . '.0.1',
        'Gecko/' . date('Ymd', rand(strtotime('2010-1-1'), time())) . ' Firefox/3.6.' . rand(1, 20),
          'Gecko/' . date('Ymd', rand(strtotime('2010-1-1'), time())) . ' Firefox/3.8'
      ));

      switch ($arch) {
      case 'lin':
          return "(X11; Linux {proc}; rv:" . rand(5, 7) . ".0) $ver";
      case 'mac':
          $osx = self::osx_version();
          return "(Macintosh; {proc} Mac OS X $osx rv:" . rand(2, 6) . ".0) $ver";
      case 'win':
      default:
          $nt = self::nt_version();
          return "(Windows NT $nt; {lang}; rv:1.9." . rand(0, 2) . ".20) $ver";

      }
  }

  protected function safari($arch) {
      $saf = rand(531, 535) . '.' . rand(1, 50) . '.' . rand(1, 7);
      if (rand(0, 1) == 0) {
          $ver = rand(4, 5) . '.' . rand(0, 1);
      } else {
          $ver = rand(4, 5) . '.0.' . rand(1, 5);
      }

      switch ($arch) {
      case 'mac':
          $osx = self::osx_version();
          return "(Macintosh; U; {proc} Mac OS X $osx rv:" . rand(2, 6) . ".0; {lang}) AppleWebKit/$saf (KHTML, like Gecko) Version/$ver Safari/$saf";
      //case 'iphone':
      //    return '(iPod; U; CPU iPhone OS ' . rand(3, 4) . '_' . rand(0, 3) . " like Mac OS X; {lang}) AppleWebKit/$saf (KHTML, like Gecko) Version/" . rand(3, 4) . ".0.5 Mobile/8B" . rand(111, 119) . " Safari/6$saf";
      case 'win':
      default:
          $nt = self::nt_version();
          return "(Windows; U; Windows NT $nt) AppleWebKit/$saf (KHTML, like Gecko) Version/$ver Safari/$saf";
      }

  }

  protected function iexplorer($arch) {
      $ie_extra = array(
          '',
          '; .NET CLR 1.1.' . rand(4320, 4325) . '',
          '; WOW64'
      );

      $nt = self::nt_version();
      $ie = self::ie_version();
      $trident = self::trident_version();
      return "(compatible; MSIE $ie; Windows NT $nt; Trident/$trident)";
  }

  protected function opera($arch) {
      $op_extra = array(
          '',
          '; .NET CLR 1.1.' . rand(4320, 4325) . '',
          '; WOW64'
      );

      $presto = self::presto_version();
      $version = self::presto_version2();

      switch ($arch) {
      case 'lin':
          return "(X11; Linux {proc}; U; {lang}) Presto/$presto Version/$version";
      case 'win':
      default:
          $nt = self::nt_version();
          return "(Windows NT $nt; U; {lang}) Presto/$presto Version/$version";
      }
  }

  protected function chrome($arch) {
      $saf = rand(531, 536) . rand(0, 2);
      $chrome = self::chrome_version();

      switch ($arch) {
      case 'lin':
          return "(X11; Linux {proc}) AppleWebKit/$saf (KHTML, like Gecko) Chrome/$chrome Safari/$saf";
      case 'mac':
          $osx = self::osx_version();
          return "(Macintosh; U; {proc} Mac OS X $osx) AppleWebKit/$saf (KHTML, like Gecko) Chrome/$chrome Safari/$saf";
      case 'win':
      default:
          $nt = self::nt_version();
          return "(Windows NT $nt) AppleWebKit/$saf (KHTML, like Gecko) Chrome/$chrome Safari/$saf";
      }
  }

  /**
   * Main function which will choose random browser
   * @param  array $lang  languages to choose from
   * @return string       user agent
   */
  public static function generate(array $lang=array('en-US')) {
      list($browser, $os) = self::chooseRandomBrowserAndOs();

      $proc = array(
          'lin' => array('i686', 'x86_64'),
          'mac' => array('Intel', 'PPC', 'U; Intel', 'U; PPC'),
          'win' => array('foo')
      );

      switch ($browser) {
    case 'firefox':
          $ua = "Mozilla/5.0 " . self::firefox($os);
          break;
      case 'safari':
          $ua = "Mozilla/5.0 " . self::safari($os);
          break;
      case 'iexplorer':
          $ua = "Mozilla/5.0 " . self::iexplorer($os);
          break;
      case 'opera':
          $ua = "Opera/" . rand(8, 9) . '.' . rand(10, 99) . ' ' . self::opera($os);
          break;
      case 'chrome':
          $ua = 'Mozilla/5.0 ' . self::chrome($os);
          break;
      }

      $ua = str_replace('{proc}', self::array_random($proc[$os]), $ua);
      $ua = str_replace('{lang}', self::array_random($lang), $ua);

      return $ua;
  }

}
