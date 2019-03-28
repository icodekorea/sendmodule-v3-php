isNewSubject = true; // Ÿ��Ʋ �ڵ� ���� ����.

/**
 * �����Ͻø� ����ð����� ���ߴ� ó��.
 * body.onload ������ ������.
 */
function setDate() {
    var date = new Date();
    document.MsgForm.R_YEAR.value = date.getFullYear();
    document.MsgForm.R_MONTH.value = fillString(date.getMonth()+1, 2, '0');
    document.MsgForm.R_DAY.value = fillString(date.getDate(), 2, '0');
    document.MsgForm.R_HOUR.value = fillString(date.getHours(), 2, '0');
    document.MsgForm.R_MIN.value = fillString(date.getMinutes(), 2, '0');
}

/**
 * ���ڿ��� ���ϴ� ���̸�ŭ Ư�� ���ڷ� ä���ִ� ���
 * ä���� ���ڴ� ���ڸ����� ����ȴ�.
 *
 * @param     str           ä������� ���ڿ�
 *            n             ���ϴ� ���ڿ��� ��ü ����
 *            chr           ä����ϴ� ����
 * @return    string        ������ ���ڿ�
 */
function fillString(str, n, chr){
    var i, c = '';
    str += ""; // ���� ������ ���� ���ڿ��� ��ȯ
    if(chr==undefined) chr = ' ';
    if(n < 0){
        for(i=str.length;i<-n;i++) c += chr;
        return str + c;
    }else{
        for(i=str.length;i<n;i++) c += chr;
        return c + str;
    }
}


/**
 * �Ѿ�� ��ȣ�� �־��� ���ڷθ� �̷�� ������ Ȯ���մϴ�.
 * �������� ���� mode ������ �з��ϴµ� '0'�� ��� ���ڸ�,
 * '1'�� ���� ',' ��ȣ�� �����ؼ� Ȯ���մϴ�.
 *
 * @param    contents    ���ڸ��ִ��� Ȯ���Ϸ� �ϴ� ��
 *            moce        �˻�����
 * @return    -true-        ���������� �̷���� �� �϶�
 *            -false-        ���ڸ��� �ƴ� ���������� ���� ���
 */
function isInteger(contents, mode) {
    var isNum = true;

    if (contents == "")
        return false;

    for (j=0; (j<contents.length); j++) {
        if (mode == 0){
            if ((contents.charAt(j) < "0")||(contents.charAt(j) > "9")) {
                isNum = false;
            }
        } else {
            if ((contents.charAt(j) < '0' || contents.charAt(j) > '9') && contents.charAt(j) != ',') {
                isNum = false;
            }
        }
    }
    return isNum;
}

/**
 * ��ȣ �� �̸�Ƽ���� �Է��մϴ�.
 * 
 * @param    str        �Է��� ��ȣ �� �̸�Ƽ��
 */ 
function add(str) {
    document.MsgForm.strData.focus();
    document.MsgForm.strData.value+=str; 
    ChkLen();
    return;
}


/**
 * ���ڼ� üũ
 * ����ȭ���� ����ɶ�(���� ���ų�, ���ﶧ ��) ���ڼ��� ����ؼ� �����ݴϴ�.
 * ���ڰ� 90����Ʈ�� �Ѿ�� ��� ������ ��� ���쵵�� �����մϴ�.
 */
function ChkLen() {
    var pos, pch, ch;
    var msglen = 0;

    // �ϰ� ���� ġȯ
    MsgForm.strData.value = MsgForm.strData.value.replace(/\u200b/g, '');

    var len = MsgForm.strData.value.length;
    for (i=0;i<len;i++){
        pos = MsgForm.strData.value.charAt(i);
        if (pos == ".") isNewTitle = false;

        // �ڸ��� ���.
        ch = escape(pos);
        if (pch == '%0D' && ch == '%0A') { } // 2byte enter (\n\r)
        else if (ch.length > 4) msglen += 2;
        else msglen++;
        pch = ch;
    }
    
    if(isNewSubject && msglen <= 30) MsgForm.strSubject.value = MsgForm.strData.value;

    MsgForm.strDataCount.value = msglen;
    
    if (msglen > 90) {
        document.getElementById("maxLength").innerHTML = "2000";
        document.getElementById("divLmsTitle").style.display = "";
        document.getElementById("msgType").innerHTML = "LMS";
        if (msglen > 2000) alert('���ڸ޽����� 2000����Ʈ�� ���� �� �����ϴ�.');
    } else {
        document.getElementById("msgType").innerHTML = "SMS";
        document.getElementById("maxLength").innerHTML = "90";
        document.getElementById("divLmsTitle").style.display = 'none';
    }
}

function chkTitle() {
    isNewSubject = false;
}

/**
 * �߼۹�ȣ �߰�
 * �߼۹�ȣ�� �߰��մϴ�. �߰��� �ϱ����� addCallNum �ڽ��� ��ȭ��ȣ�� �Է��ϰ�
 * �߰���ư�� ���� �߰��� �ϰ� �˴ϴ�. ��������� �ְ� �������� ',' ��ȣ��
 * �̿��Ͽ� ������ �߰��� �����մϴ�. �ٸ� 500���� �ѱ��� ���ϵ��� �����س�
 * �ҽ��ϴ�.
 */ 
function addItem() {
    if (MsgForm.addCallNum.value == "" ) {
        alert("�޴º� ��ȣ�� ��Ȯ�� �Է��Ͻð� �߰����ּ���.");
        MsgForm.addCallNum.value = "";
        MsgForm.addCallNum.focus();
        return;
    } 

    if (!isInteger(MsgForm.addCallNum.value, 1)) {
        window.alert('�޴»���� ���ڿ� (,)�� �Է��Ͻ� �� �ֽ��ϴ�.');            
        MsgForm.addCallNum.value = "";
        MsgForm.addCallNum.focus();
        return;
    }

    var i;
    rcvPhnId    = MsgForm.addCallNum.value;
    rcvPhnIdArr    = rcvPhnId.split(',');
    rcvPhnIdLen    = rcvPhnIdArr.length;

    if (rcvPhnIdLen > 500) {
        window.alert('500�� �̻��� ������ ������ �̿��ϽǼ� �����ϴ�.');
        MsgForm.addCallNum.value = "";
        MsgForm.addCallNum.focus();
        return;
    }else{
        for (i=0 ; i < rcvPhnIdLen; i++) {
            addSelectBox(" ", rcvPhnIdArr[i]);
            MsgForm.addCallNum.value = "";
        }
    }
}

/**
 * �߼۹�ȣ ����
 * �߼۹�ȣ�� �߼۸�Ͽ��� �����մϴ�. �����ÿ��� �߼۸�Ͽ��� ���ϴ� �׸���
 * ������ ���¿��� �մϴ�.
 */ 
function delItem() {
    var i,j;
    var Cnt;
    var recvVList = new Array();
    var recvTList = new Array();
        
    recvList = MsgForm.strDest;

    for (i = 1, Cnt = 0; i < recvList.options.length; i++) {
        if (recvList.options[i].selected == false) {
            recvVList[Cnt]        = recvList.options[i].value;
            recvTList[Cnt++]    = recvList.options[i].text;
        }
    }

    recvList.options.length = 1;
    for (i = 0; i < Cnt; i++) {
        index = recvList.options.length;
        recvList.options.length            = index+1;
        recvList.options[index].value    = recvVList[i];
        recvList.options[index].text    = recvTList[i];
    }
        
    delete(recvVList);
    delete(recvTList);
}

/**
 * �߼۹�ȣ �ڽ��� �߰�
 * ���� �Ѱܹ޾� �ڽ��� �߰��մϴ�.
 * 
 * @param    name       �߼��ڸ�
 *            tel       �߼۹�ȣ
 */ 
function addSelectBox(name, tel) {

    index = MsgForm.strDest.options.length;

    if (name.length > 8) name = name.substr(0,8);
    if (index == 0)    index = index+1;

    phoneRe = /(01[016789])-?([0-9]{3,4})-?([0-9]{4})/;
    phoneRe.exec(tel)

    if(phoneRe.exec(tel)){
        
        tel = RegExp.$1 + RegExp.$2 + RegExp.$3;

        for (i=0; i < index ; i++) {
            if (MsgForm.strDest.options[i].text == tel) {
                alert(name+"���� �̹� �����ڿ� ���ԵǾ� �ֽ��ϴ�.");
                return;
            }
        }

        MsgForm.strDest.options.length = index+1;
        MsgForm.strDest.options[index].value = tel +";";
        MsgForm.strDest.options[index].text = tel;
        MsgForm.strDest.options.length = index+1;
    } 
}   

/**
 * ������۰� �������� ���¿� ���� �� �ȿ� ���Ե� ���೯¥�Է� �κ��� ��뿩�θ� �����մϴ�.
 *
 * @param    form    ����� ���Դϴ�.
 */
function CWCheck(form) {
    
    dest = document.all["sDest"]
    if (document.MsgForm.chkSendFlag[0].checked) {    // ��������� �������� ���
        
        sDest.style.display = "none";
        document.MsgForm.R_YEAR.disabled = true;
        document.MsgForm.R_MONTH.disabled = true;
        document.MsgForm.R_DAY.disabled = true;
        document.MsgForm.R_HOUR.disabled = true;
        document.MsgForm.R_MIN.disabled = true;
    }
    else                                            // ���������� �������� ���
    {
        sDest.style.display = "block";    
        document.MsgForm.R_YEAR.disabled = false;
        document.MsgForm.R_MONTH.disabled = false;
        document.MsgForm.R_DAY.disabled = false;
        document.MsgForm.R_HOUR.disabled = false;
        document.MsgForm.R_MIN.disabled = false;
        document.MsgForm.R_YEAR.focus();
    }
    document.MsgForm.strTelList.value = "";
}

/**
 * ���ڸ� �߼��ϱ� ���Ͽ� �Ѱ� ������ �մϴ�.
 *
 * return    -false-    ������ ���з� ���ư��� �����Ⱑ ��ȿȭ �˴ϴ�.
 */
function varcheck() {

    var varStrData = document.MsgForm.strData.value;
    var varStrSubject = document.MsgForm.strSubject.value;
    var varStrCallBack = document.MsgForm.strCallBack.value;

    // �޽����� �ԷµǾ� �ִ��� Ȯ���մϴ�.
    if (varStrData == ""){
        alert("�޽����� �Է��� �ּ���");
        document.MsgForm.strData.focus();
        return false;
    } else if (varStrData.length > 2000) {
        alert("���ڴ� 2000byte�� ���� �� �����ϴ�.");
        document.MsgForm.strData.focus();
        return false;
    }

    // LMS ������ ���¸� Ȯ���ϰ� �����մϴ�.
    if (document.getElementById("divLmsTitle").style.display != "none") {
        if(varStrSubject.length == 0) {
            document.MsgForm.strSubjectrData.value = "[�������]";
        } else if (varStrSubject.length > 30) {
            alert("LMS ������ 30byte�� ���� �� �����ϴ�.");
            document.MsgForm.strSubjectrData.focus();
            return false;
        }
    }

    // �޴� ����� ��ȭ��ȣ�� �� �ִ��� Ȯ���մϴ�.
    if (document.MsgForm.strDest.length < 2){
        alert("������ ��ȣ�� �Է��� �ּ���");
        document.MsgForm.addCallNum.focus();
        return false;
    }

    // ������ ����� ��ȭ��ȣ�� �� �ִ��� Ȯ���մϴ�.
    if (varStrCallBack  == null || varStrCallBack == ""){
        alert("ȸ�Ź�ȣ�� �Է��ϼ���");
        document.MsgForm.strCallBack.focus();
        return false;
    }

    // ������ ����� ��ȭ��ȣ�� ���ڷθ� �̷�� ������ Ȯ���մϴ�.
    if (!isInteger(varStrCallBack, 0)){
        alert('ȸ�Ź�ȣ�� ���ڸ� �����մϴ�.');
        document.MsgForm.strCallBack.focus();
        return false;
    }

    // ���� �⵵�� ��Ȯ���� Ȯ���մϴ�.
    if (document.MsgForm.chkSendFlag[1].checked && document.MsgForm.R_YEAR.value.length!=4){
        alert("�⵵�� ��Ȯ���� �ʽ��ϴ�.");
        document.MsgForm.R_YEAR.focus();
        return false;
    }

    // ���� ���� ��Ȯ���� Ȯ���մϴ�.
    if (document.MsgForm.chkSendFlag[1].checked && document.MsgForm.R_MONTH.value.length!=2){
        alert("���� ��Ȯ���� �ʽ��ϴ�.");
        document.MsgForm.R_MONTH.focus();
        return false;
    }

    // ���� ���� ��Ȯ���� Ȯ���մϴ�.
    if (document.MsgForm.chkSendFlag[1].checked && document.MsgForm.R_DAY.value.length!=2){
        alert("���� ��Ȯ���� �ʽ��ϴ�.");
        document.MsgForm.R_DAY.focus();
        return false;
    }

    document.MsgForm.strTelList.value = "";
    for (k=1; k < document.MsgForm.strDest.length; k++) { 
        document.MsgForm.strTelList.value += document.MsgForm.strDest.options[k].value;
    }
}

/**
 * �� �ʱ�ȭ �Լ�.
 */
function reset() {
    document.MsgForm.reset();
}

