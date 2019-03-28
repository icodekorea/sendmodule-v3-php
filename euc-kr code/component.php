<?php
/**
* SMS �߼��� �����ϴ� ���� Ŭ�����̴�.
*
* ����, �߼�, URL�߼�, ������� ���������� ���̴� ��� �κ��� ���ԵǾ� �ִ�.
*/
class SMS {
  var $icode_key;
  var $socket_host;
  var $socket_port;
  var $Data = array();
  var $Result = array();

  // SMS ���� ����
  function SMS_con($host, $port, $key) {
    $this->socket_host = $host;
    $this->socket_port = $port;
    $this->icode_key = $key;
  }
    
  function Init() {
    $this->Data = array();    // �߼��ϱ� ���� ��Ŷ������ �迭�� ����.
    $this->Result = array();    // �߼۰������ �迭�� ����.
  }

  /**
   * �߼� ��Ŷ ����
   * Add(���Ź�ȣ���(�迭), �߽Ź�ȣ, �߼۳���(2000���̳�), ����(�ɼ�, 30���̳�), ��������(�ɼ�, 12�ڸ�)
   */
  function Add($strTelList, $strCallBack, $strData, $strSubject='', $strDate='') {
    // ����ġȯ
    $strData = preg_replace("/\r\n/","\n",$strData);
    $strData = preg_replace("/\r/","\n",$strData);

    // ���� Ÿ�Ժ� Port ����.
    $sendType = strlen($strData)>90 ? 1 : 0; // 0: SMS / 1: LMS
    if($sendType==0) $strSubject = "";

    $strCallBack = CutChar($strCallBack, 12);       // ȸ�Ź�ȣ
      
    /** LMS ���� **/
    /*
    �����ʵ��� ���� ���� ��� �ܸ��� ������ ������ ���� ǥ�� ����� �ٸ�
    1.�������� �����ʵ庸�� ���� Disable -> �����ʵ尪�� �־ ��ǥ��
    2.�������� �����ʵ庸�� ���� Enable  -> ������ ���� ���� ��� ����������� �ڵ�ǥ��
            
    ������ ù���ڿ� "<",">", ���๮�ڰ� ������� �ܸ������� �� ��Ż翡 ���� �޼��� ���۽��� -> ���ڸ� üũ�ϰų� ��ȯó�����
    $strSubject = str_replace("\r\n", " ", $strSubject); 
    $strSubject = str_replace("<", "[", $strSubject); 
    $strSubject = str_replace(">", "]", $strSubject); 
    */

    $strSubject = CutChar($strSubject,30);
    $strData    = CutChar($strData,2000);

    /* �ʼ� �׸� ���� �������� �ڵ����� �˻� ����.
    ���� ��Ŀ� ���� Ȱ�� 
    $Error = CheckCommonTypeDest($strTelList); // ��ȣ �˻�
    $Error = IsVaildCallback($strCallBack);
    $Error = CheckCommonTypeDate($strDate);
    */

    foreach ($strTelList as $tel) {
      if(empty($tel)) continue;
      $list = array(
        "key" => $this->icode_key, 
        "tel" => $tel,
        "cb" => $strCallBack,
        "msg" => iconv("EUC-KR", "UTF-8", $strData)
      );
      if(!empty($strSubject)) $list['title'] = iconv("EUC-KR", "UTF-8", $strSubject);
      if(!empty($strDate)) $list['date'] = $strDate;
      $packet = json_encode($list);
      $this->Data[] = '06'.str_pad(strlen($packet), 4, "0", STR_PAD_LEFT).$packet;
    }
    return true; 
  }

  /**
   * ���ڹ߼� �� ��������� �����մϴ�.
   */
  function Send() {
    $fsocket = fsockopen($this->socket_host,$this->socket_port, $errno, $errstr, 2);
    if (!$fsocket) return false;
    set_time_limit(300);

    foreach($this->Data as $puts) {
      fputs($fsocket, $puts);
      while(!$gets) { $gets = fgets($fsocket,32); }

      $chk = preg_match("/\"tel\":\"([0-9]*)\"/", substr($puts,6), $matches);
      $desc = $matches[1];
      $resultCode = substr($gets,6,2);
      if ($resultCode == '00' || $resultCode == '17') { // 17�� ����(�߼�)���.
        $this->Result[] = $resultCode.":".substr($gets,8,12).":".substr($gets,20,11);

      } else {
        $this->Result[] = $desc.":Error(".substr($gets,6,2).")";
        if(substr($gets,6,2) >= "80") break;
      }
      $gets = "";
    }

    fclose($fsocket);
    $this->Data = array();
    return true;
  }
}

/**
 * ���ϴ� ���ڿ��� ���̸� ���ϴ� ���̸�ŭ ������ �־� ���ߵ��� �մϴ�.
 *
 * @param   text  ���ϴ� ���ڿ��Դϴ�.
 *          size  ���ϴ� �����Դϴ�.
 * @return        ����� ���ڿ��� �ѱ�ϴ�.
 */
function FillSpace($text,$size) {
  for ($i=0; $i<$size; $i++) $text.= " ";
  $text = substr($text,0,$size);
  return $text;
}

/**
 * ���ϴ� ���ڿ��� ���ϴ� �濡 �´��� Ȯ���ؼ� �����ϴ� ����� �մϴ�.
 *
 * @param   word  ���ϴ� ���ڿ��Դϴ�.
 *          cut   ���ϴ� �����Դϴ�.
 * @return        ����� ���ڿ��Դϴ�.
 */
function CutChar($word, $cut) {
  $word=substr($word,0,$cut); // �ʿ��� ���̸�ŭ ����.
  for ($k = $cut-1; $k > 1; $k--) {     
    if (ord(substr($word,$k,1))<128) break; // �ѱ۰��� 160 �̻�.
  }
  $word = substr($word, 0, $cut-($cut-$k+1)%2);
  return $word;
}

function CutCharUtf8($word, $cut) {
  preg_match_all('/[\xE0-\xFF][\x80-\xFF]{2}|./', $word, $match); // target for BMP

  $m = $match[0];
  $slen = strlen($word); // length of source string
  if ($slen <= $cut) return $word;
  
  $ret = array();
  $count = 0;
  for ($i=0; $i < $cut; $i++) {
      $count += (strlen($m[$i]) > 1)?2:1;
      if ($count > $cut) break;
      $ret[] = $m[$i];
  }

  return join('', $ret);
}


/**
 * �߸��� ���Ź�ȣ ����� �����մϴ�.
 *
 * @param   strTelList  �߼۹�ȣ �迭.
 * @return              �߸��� ���Ź�ȣ ���.
 */
function CheckCommonTypeDest($strTelList) {
  $result = '';
  foreach ($strTelList as $tel) {
    $tel = preg_replace("/[^0-9]/","",$tel);
    if(!preg_match("/^(0[173][0136789])([0-9]{3,4})([0-9]{4})$/", $tel)) $result .= $tel.',';
  }
  return $result;
}


/**
 * ȸ�Ź�ȣ ��ȿ�� ������ȸ 
 * �ѱ����ͳ������ �ǰ����
 *
 * @param  string callback  ȸ�Ź�ȣ
 * @return                  ó������Դϴ�
 */
function IsVaildCallback($callback){
  $_callback = preg_replace('/[^0-9]/', '', $callback);
  if (!preg_match("/^(02|0[3-6]\d|01(0|1|3|5|6|7|8|9)|070|080|007)\-?\d{3,4}\-?\d{4,5}$/", $_callback) && 
    !preg_match("/^(15|16|18)\d{2}\-?\d{4,5}$/", $_callback)) return "ȸ�Ź�ȣ����";    
  if (preg_match("/^(02|0[3-6]\d|01(0|1|3|5|6|7|8|9)|070|080)\-?0{3,4}\-?\d{4}$/", $_callback)) return "ȸ�Ź�ȣ����";
  return '';
}

/**
 * ���ڿ��� JSON ��밡�� Ÿ������ ��ȯ�Ѵ�.
 */
function EscapeJsonString($value) {
  $escapers =     array('\\',  '"');
  $replacements = array('\\\\', '\"');
  $result = str_replace($escapers, $replacements, $value);
  return $result;
}

/**
 * ���೯¥�� ���� ��Ȯ�� ������ Ȯ���մϴ�.
 *
 * @param   string strDate  ����ð�
 * @return                  ó������Դϴ�
 */
function CheckCommonTypeDate($strDate) {
  $strDate = preg_replace("/[^0-9]/", "", $strDate);
  if ($strDate){
    if(strlen($strDate) != 12) return '���೯¥����';
    if (!checkdate(substr($strDate,4,2),substr($strDate,6,2),substr($rsvTime,0,4))) return "���೯¥����";        
    if (substr($strDate,8,2)>23 || substr($strDate,10,2)>59) return "����ð�����";        
  }
  return '';
}
?>