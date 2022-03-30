<?php

namespace PonderSource\WSSE;

use JMS\Serializer\Annotation\{XmlNamespace,XmlAttribute,SerializedName,XmlList,XmlElement};

/**
 * @XmlNamespace(uri="http://www.w3.org/2000/09/xmldsig#")
 */
class DSigReference {

    /**
     * @XmlAttribute
     * @SerializedName("URI")
     */
    private $uri;

    /**
     * @XmlList(entry="ds:Transform") 
     * @SerializedName("ds:Transforms")
     */
    private $transforms;

    /**
     * @SerializedName("ds:DigestMethod")
     * @XmlElement(cdata=false, namespace="http://www.w3.org/2000/09/xmldsig#")
     */
    private $digestMethod;

    /**
     * @SerializedName("ds:DigestValue")
     * @XmlElement(cdata=false, namespace="http://www.w3.org/2000/09/xmldsig#")
     */
    private $digestValue;

    public function __construct($uri, $content, $transforms, $digestMethod){
        $this->uri = $uri;
        $this->digestMethod = $digestMethod;
        $this->transforms = $transforms;
        foreach($transforms as $transform){
            $content = $transform->transform($content);
        }
        $this->digestValue = $digestMethod->getDigest($content);
    }
}