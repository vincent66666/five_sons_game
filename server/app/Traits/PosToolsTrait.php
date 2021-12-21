<?php

declare(strict_types=1);
/**
 * @Created by PhpStorm
 * @User    : 清风醉
 */
namespace App\Traits;

trait PosToolsTrait
{
    /**
     * 普通数组转xml.
     *
     * @param $postStr
     *
     * @return mixed
     */
    public function XMLDataParse($postStr)
    {
        $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
        $jsonStr = json_encode($postObj);
        return json_decode($jsonStr, true);
    }

    /**
     * @param $resp
     * @param string $format
     *
     * @param bool $is_attributes
     *
     * @return array|bool|mixed|SimpleXMLElement
     */
    public function parserXMLToArray($resp, $format = 'object', $is_attributes = false)
    {
        $xml_parser = xml_parser_create();
        if (! xml_parse($xml_parser, $resp, true)) {
            xml_parser_free($xml_parser);
            return false;
        }

        $disableLibxmlEntityLoader = libxml_disable_entity_loader(true);
        $respObject                = simplexml_load_string(
            $resp,
            'SimpleXMLElement',
            LIBXML_NOCDATA | LIBXML_NOBLANKS | LIBXML_NOERROR
        );
        libxml_disable_entity_loader($disableLibxmlEntityLoader);

        if ($respObject === false) {
            return false;
        }

        if ($format === 'array') {
            return $this->xmlObjectToArray($respObject, $is_attributes);
        }

        return $respObject;
    }

    /**
     * @param $object
     *                xml对象转array，解决xml空元素的情况下转成空数组的问题
     * @param bool $is_attributes
     *
     * @return array|mixed
     */
    public function xmlObjectToArray($object, $is_attributes = false)
    {
        $result = [];
        if (is_object($object)) {
            $object = get_object_vars($object);
        }

        if (is_array($object)) {
            foreach ($object as $key => $vo) {
                if (is_object($vo)) {
                    $vo = $this->xmlObjectToArray($vo, $is_attributes);
                }

                if ($is_attributes) {
                    if ($key == '@attributes') {
                        $result = $vo;
                    } else {
                        $result[$key] = $vo;
                    }
                } else {
                    if ($key != '@attributes') {
                        $result[$key] = $vo == [] ? '' : $vo;
                    }
                }
            }
        }
        return $result;
    }

    /**
     * 转换数组为 SimpleXMLElement.
     *
     * 数组键名转换为 nodeName(value为数组时) 或 attributeName(value非array时)
     * 数组键名为 _ 时转换为 innerText
     *
     * 数组值转换为 nodeValue或attribute
     * 数组值为数组时转换为子节点
     * 数组值为 null 时转换为空节点
     *
     * todo array.value 非数组时验证是否为标量数据类型
     *
     * @param array $arr 数据数组
     * @param SimpleXMLElement $xml SimpleXMLElement 实例的引用
     * @param string $nodeName 递归中调用参数，调用时不需要提供
     */
    public function arrayToXMLAttribute(array $arr, \SimpleXMLElement &$xml, $nodeName = '')
    {
        foreach ($arr as $key => $val) {
            if (is_array($val)) {
                if (is_numeric($key)) {
                    $simpleXMLElement = $xml->addChild($nodeName);
                    $this->arrayToXMLAttribute($val, $simpleXMLElement);
                } else {
                    if (! isset($val[0])) {
                        $simpleXMLElement1 = $xml->addChild($key);
                        $this->arrayToXMLAttribute($val, $simpleXMLElement1);
                    } else {
                        $this->arrayToXMLAttribute($val, $xml, $key);
                    }
                }
            } else {
                if ($key !== '_') {
                    if (! is_null($val)) {
                        $xml->addAttribute($key, (string) $val);
                    } else {
                        $xml->addChild($key);
                    }
                } else {
                    $xml->{0} = $val;
                }
            }
        }
    }
}
