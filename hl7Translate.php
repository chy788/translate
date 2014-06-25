<?php
if($_FILES){
    
    if($_FILES["error"] != 0){
        echo "�ϴ�ʧ��";
        exit;
    }
    if($_FILES["file"]["type"][0] != "text/xml"){
        echo "�ϴ����ʹ���,���ϴ�xml�ĵ�";
        exit;
    }
    $tmp_name = $_FILES["file"]["tmp_name"][0];
    $target = __DIR__."/test.xml";
    if(!move_uploaded_file($tmp_name, $target)){
        echo "�ϴ�ʧ��,��ȷ���ϴ�������ȷxml����";
        exit;
    }


$dom = new DOMDocument();
$dom->load($target);

$arrContent =array();
$hl7Version = $dom->documentElement->namespaceURI;
if(!preg_match("/hl7-org:v3/",$hl7Version)){
    echo "���ϴ���ȷ�淶��hl7 v3�汾��xml";
    exit;
}
getArray($dom->documentElement);
if(count($arrContent)<1){
    echo "��ȷ���ϴ�������ȷ���ݵ�xml����";
}
echo "�ϴ�hl7 xml�汾Ϊ:".substr($hl7Version,4,strlen($hl7Version));
echo "<table>";
echo "<tr><td>��ǩ����</td><td>��ǩ����</td></tr>";
foreach($arrContent as $k => $v){
    echo "<tr>";
    echo "<td>$k</td>";
    echo "<td>$v</td>";
    echo "</tr>";
}
echo "</table>";
echo "<br>";
print_r($arrContent);
}
function getArray($node) {
    $array = false;
    global $arrContent;
    if ($node->hasAttributes()) {
        foreach ($node->attributes as $attr) {
            $array['attr'][$attr->nodeName] = $attr->nodeValue;
            //$array[$attr->nodeName] = $attr->nodeValue;
        }
    }

    if ($node->hasChildNodes()) {
        if ($node->childNodes->length == 1) {
            $array[$node->firstChild->nodeName] = getArray($node->firstChild);
            
            //������ӽڵ�����ȫ���ı�(����ǩ����,�������е�login)
            if($node->firstChild instanceof DOMText){
                $arrContent[$node->nodeName] = $node->firstChild->nodeValue;
                
            }
        } else {
            foreach ($node->childNodes as $childNode) {
                if ($childNode->nodeType != XML_TEXT_NODE) {
                    //��ǰ�����Ľڵ��ǲ�������ǩ���ݵĽڵ� (�ų����и���
                    $array[$childNode->nodeName] = getArray($childNode);
                }
            }
        }
    } else {
        return $node->nodeValue;
    }
    return $array;
}
?>
<html>
<body>
    <form action="#" method="post" enctype="multipart/form-data">
    <input type="file" name="file[]"/><br>
    <input type="submit" value="upload"/>
</form>
</body>
</html>
