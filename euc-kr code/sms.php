<!doctype html>
<html lang="ko">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=euc-kr">
  <title>icodekorea sms module</title>
</head>
<body>
<?php

include "conf/config.php";
include "component.php";

/**
 * �߽Ź�ȣ ��������� (������Ż���� ��84��)
 *  �������� ǥ�õ� ��ȭ��ȣ�� ���� �̿��� ���� ������ ���Ͽ� ���� ���۽� 
 *  ���� ������ �߽Ź�ȣ�θ� ����� �� �ֵ��� ����ϴ� �����Դϴ�.
 *  �߽Ź�ȣ����� �����ڵ� ����Ʈ �α��� �� ��� �߽Ź�ȣ ��ϸ� ���� �Ͻñ� �ٶ��ϴ�.
*/

$SMS = new SMS;    /* SMS ��� Ŭ���� ���� */
$SMS->SMS_con($socket_host,$socket_port,$icode_key);    /* �����ڵ� ���� ���� */

/**
 * ���ڹ߼� Form�� ������� �ʰ� �ڵ� �߼��� ��� ���Ź�ȣ�� 1���� ��� ��ȣ �������� ";"�� ���δ� 
 * ex) $strTelList = "0100000001;";
*/
$strTelList     = $_POST["strTelList"];    /* ���Ź�ȣ : 01000000001;0100000002; */
$strCallBack    = $_POST["strCallBack"];  /* �߽Ź�ȣ : 0317281281 */
$strSubject     = $_POST["strSubject"];    /* LMS����  : LMS�߼ۿ� �̿�Ǵ� ����( component.php 60������ ���� �ٶ��ϴ�. */
$strData        = $_POST["strData"];        /* �޼��� : �߼��Ͻ� ���� �޼��� */

$chkSendFlag    = $_POST["chkSendFlag"];  /* ���� ������ : 0 �������, 1 ����߼� */
$R_YEAR         = $_POST["R_YEAR"];         /* ���� : ��(4�ڸ�) 2016 */
$R_MONTH        = $_POST["R_MONTH"];        /* ���� : ��(2�ڸ�) 01 */
$R_DAY          = $_POST["R_DAY"];          /* ���� : ��(2�ڸ�) 31 */
$R_HOUR         = $_POST["R_HOUR"];         /* ���� : ��(2�ڸ�) 02 */
$R_MIN          = $_POST["R_MIN"];          /* ���� : ��(2�ڸ�) 59 */

$strTelList  = explode(";",$strTelList);

// ���༳���� �մϴ�.
if ($chkSendFlag) $strDate = $R_YEAR.$R_MONTH.$R_DAY.$R_HOUR.$R_MIN;
else $strDate = "";

// ���� �߼ۿ� �ʿ��� �׸��� �迭�� �߰�
$result = $SMS->Add($strTelList, $strCallBack, $strData, $strSubject, $strDate);

// ��Ŷ ������ ����� ���� �߼ۿ��θ� �����մϴ�.
if ($result) {
  echo "�Ϲݸ޽��� �Է� ����<br />";
  echo "<hr>";

  // ��Ŷ�� �������̶�� �߼ۿ� �õ��մϴ�.
  $result = $SMS->Send();

  if ($result) {
    echo "������ �����߽��ϴ�.<br /><br />";
    $success = $fail = 0;
    $isStop = 0;
    foreach($SMS->Result as $result) {

      list($phone,$code)=explode(":",$result);

      if (substr($code,0,5)=="Error") {
        echo $phone.' �߼ۿ���('.substr($code,6,2).'): ';
        switch (substr($code,6,2)) {
          case '23':   // "23:�����Ϳ���, ���۳�¥����, �߽Ź�ȣ�̵��"
            echo "�����͸� �ٽ� Ȯ���� �ֽñ�ٶ��ϴ�.<br>";
            break;

          // �Ʒ��� �������� �߼������� �ߴܵ�.
          case '85':   // "85:�߼۹�ȣ �̵��"
            echo "��ϵ��� �ʴ� �߼۹�ȣ �Դϴ�.<br />";
            break;
          case '87':   // "87:��������"
            echo "(������-���Ȯ��)���� ���� ���Ͽ����ϴ�.<br />";
            break;
          case '88':   // "88:������� �߼ۺҰ�"
            echo "������� ����� �Ұ����մϴ�. �����ڵ�� �����ϼ���.<br />";
            break;

          case '96':   // "96:��ū �˻� ����"
            echo "����� �� ���� ��ūŰ�Դϴ�.<br />";
            break;
          case '97':   // "97:�ܿ����κ���"
            echo "�ܿ������� �����մϴ�.<br />";
            break;
          case '98':   // "98:���Ⱓ����"
            echo "���Ⱓ�� ����Ǿ����ϴ�.<br />";
            break;
          case '99':   // "99:��������"
            echo "���� ����� �Ұ����մϴ�. �����ڵ�� �����ϼ���.<br />";
            break;
          default:   // "�� Ȯ�� ����"
            echo "�� �� ���� ������ ������ �����Ͼ����ϴ�.<br />";
            break;
        }
        $fail++;
      } else {
        $resultString = '';
        switch (substr($code,0,2)) {
          case '17':   // "17: ����(�߼�)��� ó��. �����ؼҽ� �߼۵�."
            echo "����(�߼�)���ó�� �Ǿ����ϴ�.";
            break;
          default:   // "00: ���ۿϷ�."
            echo "���۵Ǿ����ϴ�.<br />";
            break;
        }
        echo $phone.'�� '.$resultString.' (msg seq : '.$code.')<br />';
        $success++;
      }
    }
    echo '<br />'.$success."���� ���������� ".$fail."���� ������ ���߽��ϴ�.<br />";
    $SMS->Init(); // �����ϰ� �ִ� ������� ����ϴ�.
  }
  else echo "����: SMS ������ ����� �Ҿ����մϴ�.<br />";
}
?>
</body>
</html>