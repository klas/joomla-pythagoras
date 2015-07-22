<?php
// @codingStandardsIgnoreFile
// @codeCoverageIgnoreStart
// this is an autogenerated file - do not edit
spl_autoload_register(
    function($class) {
        static $classes = null;
        if ($classes === null) {
            $classes = array(
                'content' => '/dynamic-renderer.php',
                'contenttype' => '/dynamic-renderer.php',
                'dynamicrendererimplementation' => '/dynamic-renderer.php',
                'joomla\\cms\\application\\application' => '/Application/Application.php',
                'joomla\\cms\\application\\factory' => '/Application/Factory.php',
                'joomla\\cms\\cli\\argvparser' => '/Cli/ArgvParser.php',
                'joomla\\cms\\cli\\input' => '/Cli/Input.php',
                'joomla\\cms\\cli\\output' => '/Cli/Output.php',
                'joomla\\cms\\http\\acceptheader' => '/Http/Header/AcceptHeader.php',
                'joomla\\cms\\http\\acceptlanguageheader' => '/Http/Header/AcceptLanguageHeader.php',
                'joomla\\cms\\http\\client\\scriptstrategy' => '/Http/Client/ScriptStrategy.php',
                'joomla\\cms\\http\\qualifiedheader' => '/Http/Header/QualifiedHeader.php',
                'joomla\\cms\\http\\request' => '/Http/Request.php',
                'joomla\\cms\\http\\response' => '/Http/Response.php',
                'joomla\\cms\\input\\argvinput' => '/Input/ArgvInput.php',
                'joomla\\cms\\input\\arrayinput' => '/Input/ArrayInput.php',
                'joomla\\cms\\input\\input' => '/Input/Input.php',
                'joomla\\cms\\input\\inputargument' => '/Input/InputArgument.php',
                'joomla\\cms\\input\\inputdefinition' => '/Input/InputDefinition.php',
                'joomla\\cms\\input\\inputinterface' => '/Input/InputInterface.php',
                'joomla\\cms\\input\\inputoption' => '/Input/InputOption.php',
                'joomla\\cms\\output' => '/Output.php',
                'joomla\\cms\\payload' => '/Payload.php',
                'joomla\\cms\\renderer' => '/Renderer.php',
                'joomla\\cms\\renderer\\ansirenderer' => '/Renderer/AnsiRenderer.php',
                'joomla\\cms\\renderer\\docbookrenderer' => '/Renderer/DocbookRenderer.php',
                'joomla\\cms\\renderer\\dynamicrendererimplementation' => '/Renderer/DynamicRendererImplementation.php',
                'joomla\\cms\\renderer\\factory' => '/Renderer/Factory.php',
                'joomla\\cms\\renderer\\htmlrenderer' => '/Renderer/HtmlRenderer.php',
                'joomla\\cms\\renderer\\jsonrenderer' => '/Renderer/JsonRenderer.php',
                'joomla\\cms\\renderer\\notfoundexception' => '/Renderer/NotFoundException.php',
                'joomla\\cms\\renderer\\pdfrenderer' => '/Renderer/PdfRenderer.php',
                'joomla\\cms\\renderer\\plainrenderer' => '/Renderer/PlainRenderer.php',
                'joomla\\cms\\renderer\\xmlrenderer' => '/Renderer/XmlRenderer.php',
                'joomla\\cms\\rest\\request' => '/Rest/Request.php',
                'joomla\\cms\\rest\\response' => '/Rest/Response.php',
                'joomla\\cms\\router' => '/Router.php',
                'joomla\\cms\\router\\notfoundexception' => '/Router/NotFoundException.php',
                'joomla\\cms\\router\\route' => '/Router/Route.php',
                'joomla\\command\\command' => '/Command/Command.php',
                'joomla\\command\\commandprocessor' => '/Command/CommandProcessor.php',
                'joomla\\command\\controller' => '/Command/Controller.php',
                'joomla\\command\\dispatcher' => '/Command/Dispatcher.php',
                'joomla\\command\\macrocommand' => '/Command/MacroCommand.php',
                'joomla\\command\\recoverablecommand' => '/Command/RecoverableCommand.php',
                'joomla\\content\\content' => '/Content/Content.php',
                'joomla\\content\\contentgroup' => '/Content/ContentGroup.php',
                'joomla\\content\\contentitem' => '/Content/ContentItem.php',
                'newcontenttype' => '/dynamic-renderer.php',
                'othercontenttype' => '/dynamic-renderer.php',
                'renderer' => '/dynamic-renderer.php',
                'unregisteredcontenttype' => '/dynamic-renderer.php'
            );
        }
        $cn = strtolower($class);
        if (isset($classes[$cn])) {
            require __DIR__ . $classes[$cn];
        }
    }
);
// @codeCoverageIgnoreEnd
