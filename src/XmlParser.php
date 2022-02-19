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
     * @var string $xmlstring //XML-Data as a String
     */
    protected $xmlstring;

    /**
     * @var string $namespace //Namespace of XML-Data
     */
    protected $namespace;

    /**
     * @var string $metadataDir // Path to Metadata
     */
    protected $metadataDir;

    /**
     * @var string $classes // Namespace to Classes
     */
    protected $classes;

    /**
     * @var Serializer $serializer
     */
    protected $serializer;

    /**
     * @var bool $debug
     */
    protected $debug;


    /**
     * XmlToObject constructor.
     *
     * @param string    $metadataDir
     * @param string    $classNamespace
     * @param bool|null $debug
     *
     * @return void
     */
    public function __construct(string $metadataDir, string $classNamespace, ?bool $debug = false)
    {
        $this->metadataDir = $metadataDir;
        $this->classes     = $classNamespace;
        $this->debug       = $debug;

        $this->getSerializer();
    }

    /**
     * @return Serializer
     */
    protected function getSerializer(): Serializer
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
     * @param string $xmlstring
     * @param string $namespace
     *
     * @return mixed
     */
    public function toObject(string $xmlstring = "", string $namespace = "")
    {
        $this->xmlstring = $xmlstring;
        $this->namespace = $namespace;

        // deserialize the XML into Demo\MyObject object
        return $this->serializer->deserialize($this->xmlstring, $this->namespace, 'xml');
    }

    public function toXml(object $object): string
    {
        return $this->serializer->serialize($object, 'xml');
    }

}
