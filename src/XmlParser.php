<?php


namespace MediaStoreNet\XmlParser;

use Doctrine\Common\Annotations\AnnotationReader;
use GoetasWebservices\Xsd\XsdToPhpRuntime\Jms\Handler\BaseTypesHandler;
use GoetasWebservices\Xsd\XsdToPhpRuntime\Jms\Handler\XmlSchemaDateHandler;
use JMS\Serializer\Handler\HandlerRegistryInterface;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;

/**
 * Class XmlParser
 *
 * @package MediaStoreNet\XmlParser
 */
class XmlParser
{

    /**
     * @var $xmlstring string //XML-Data as a String
     */
    protected $xmlstring;

    /**
     * @var $namespace string //Namespace of XML-Data
     */
    protected $namespace;

    /**
     * @var $metadataDir string // Path to Metadata
     */
    protected $metadataDir;

    /**
     * @var $classes string // Namespace to Classes
     */
    protected $classes;

    /**
     * @var $serializer Serializer
     */
    protected $serializer;

    /**
     * @var $debug boolean
     */
    protected $debug;


    /**
     * XmlToObject constructor.
     *
     * @param $xmlstring
     * @param $namespace
     */
    public function __construct(string $metadataDir, string $classNamespace, ?bool $debug = false)
    {
        $this->metadataDir = $metadataDir;
        $this->classes     = $classNamespace;
        $this->debug       = $debug;

        $this->getSerializer();

        return $this;
    }

    /**
     * @return \JMS\Serializer\Serializer
     * @throws \Doctrine\Common\Annotations\AnnotationException
     */
    protected function getSerializer()
    {
        $serializerBuilder = SerializerBuilder::create();
        $serializerBuilder->addMetadataDir($this->metadataDir, $this->classes);
        $serializerBuilder->configureHandlers(function (HandlerRegistryInterface $handler) use ($serializerBuilder) {
            $serializerBuilder->addDefaultHandlers();
            $handler->registerSubscribingHandler(new BaseTypesHandler());     // XMLSchema List handling
            $handler->registerSubscribingHandler(new XmlSchemaDateHandler()); // XMLSchema date handling
            // $handler->registerSubscribingHandler(new YourhandlerHere());
        });
        $serializerBuilder->setAnnotationReader(new AnnotationReader());
        $serializerBuilder->setDebug($this->debug);

        //print_r();
        return $this->serializer = $serializerBuilder->build();
    }

    /**
     * @return mixed
     */
    public function toObject(string $xmlstring = "", string $namespace = "")
    {
        $this->xmlstring = $xmlstring;
        $this->namespace = $namespace;

        // deserialize the XML into Demo\MyObject object
        return $this->serializer->deserialize($this->xmlstring, $this->namespace, 'xml');
    }

    public function toXml($object)
    {
        return $this->serializer->serialize($object, 'xml');
    }

}
