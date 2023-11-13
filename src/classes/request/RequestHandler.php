<?php

namespace Acme\classes\request;

use Acme\classes\model\Model;
use Acme\classes\renderer\IRendererInterface;

abstract class RequestHandler
{
    private array $requestData;
    protected string $acceptType;
    protected string $resource;
    protected string $identifier;
    protected string $fields;
    protected array $data;
    protected Model $obj;
    protected array $responseData;

    /**
     * Init this request handler: it needs a Request-object to handle
     *
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->acceptType = $request->getAcceptType();
        $this->requestData = $request->getData();

        $this->resource = $this->requestData['resource'];
        if(isset($this->requestData['identifier'])) { $this->identifier = $this->requestData['identifier']; }
        if(isset($this->requestData['fields'])) { $this->fields = $this->requestData['fields']; }
        $this->data = $this->requestData['data'];

        // Get table model object
        $class = 'Acme\\classes\\model\\' . ucfirst(strtolower($this->resource)) . "Model";
        $this->obj = new $class();
    }

    /**
     * Process the request: delete, read, update or add
     *
     * @return void
     */
    abstract public function processRequest(): void;

    /**
     * Send the result of the processed request: it needs a Renderer
     *
     * @param IRendererInterface $renderer
     * @return string
     */
    abstract public function sendResponse(IRendererInterface $renderer): string;
}