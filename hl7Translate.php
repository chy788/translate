<?php
if($_FILES){
    
    if($_FILES["error"] != 0){
        echo "上传失败";
        exit;
    }
    if($_FILES["file"]["type"][0] != "text/xml"){
        echo "上传类型错误,请上传xml文档";
        exit;
    }
    $tmp_name = $_FILES["file"]["tmp_name"][0];
    $target = __DIR__."/test.xml";
    if(!move_uploaded_file($tmp_name, $target)){
        echo "上传失败,请确保上传的是正确xml病例";
        exit;
    }


$dom = new DOMDocument();
$dom->load($target);

$arrContent =array();
$hl7Version = $dom->documentElement->namespaceURI;
if(!preg_match("/hl7-org:v3/",$hl7Version)){
    echo "请上传正确规范的hl7 v3版本的xml";
    exit;
}
getArray($dom->documentElement);
if(count($arrContent)<1){
    echo "请确保上传的是正确内容的xml病例";
}
echo "上传hl7 xml版本为:".substr($hl7Version,4,strlen($hl7Version));
echo "<table>";
echo "<tr><td>标签名称</td><td>标签内容</td></tr>";
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
            
            //如果其子节点是完全的文本(即标签内容,如样例中的login)
            if($node->firstChild instanceof DOMText){
                $arrContent[$node->nodeName] = $node->firstChild->nodeValue;
                
            }
        } else {
            foreach ($node->childNodes as $childNode) {
                if ($childNode->nodeType != XML_TEXT_NODE) {
                    //当前遍历的节点是不包含标签内容的节点 (排出空行干扰
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
