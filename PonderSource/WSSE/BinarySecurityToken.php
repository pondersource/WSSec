<?php

namespace PonderSource\WSSE;

use JMS\Serializer\Annotation\{XmlRoot, XmlAttribute, SerializedName, XmlValue, Exclude};
use phpseclib3\Crypt\RSA;
use phpseclib3\Crypt\PublicKeyLoader;
use phpseclib3\File\X509;
/**
 * @XmlRoot("wsse:BinarySecurityToken")
 */
class BinarySecurityToken {
    /**
     * @XmlAttribute
     * @SerializedName("EncodingType")
     */
    private $encodingType = 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-soap-message-security-1.0#Base64Binary';

    /**
     * @XmlAttribute
     * @SerializedName("ValueType")
     */
    private $valueType = 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-x509-token-profile-1.0#x509v3';

    /**
     * @XmlAttribute
     * @SerializedName("wsu:Id")
     */
    private $id;
    
    /**
     * @Exclude
     */
    private $x509Certificate;
    
    /**
     * @XmlValue(cdata=false)
     */
    private $encryptionToken;

    public function __construct($id, $cert){
        $this->id = $id;
        $this->setCertificate($cert);
    }

    public function setEncoding($encoding){
        $this->encodingType = $encoding;
        return $this;
    }
    public function getEncoding(){
        return $this->encodingType;
    }
    public function setValueType($valueType){
        $this->valueType = $valueType;
        return $this;
    }
    public function getValueType(){
        return $this->valueType;
    }
    public function setId($id){
        $this->id = $id;
        return $this;
    }
    public function getId(){
        return $this->id;
    }
    public function setCertificate($x509){
        if(get_class($x509) === 'phpseclib3\File\X509'){
            $this->x509Certificate = $x509;
            $this->encryptionToken = $this->certificate2Token($x509);
            return $this;
        }
        return false;
    }
    public function getCertificate(){
        if(isset($this->x509Certificate)){
            return $this->x509Certificate;
        } else if(isset($this->encryptionToken)){
            $this->x509Certificate = $this->token2Certificate($this->encryptionToken);
            return $this->x509Certificate;
        } else {
            return null;
        }
    }
    private function token2Certificate($token){
        $x509 = new X509;
        $x509->loadX509($token);
        return $x509;
    }
    private function certificate2Token($x509){
        $token = $x509->saveX509($x509->getCurrentCert());
        $tokenArray = explode("\r\n", $token);
        $tokenArray = array_slice($tokenArray, 1, count($tokenArray)-2);
        $token = join('', $tokenArray);
        return $token;
    }
}