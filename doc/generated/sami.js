(function(root) {

    var bhIndex = null;
    var rootPath = '';
    var treeHtml = '    <ul>                <li data-name="namespace:Specification" class="opened">                    <div style="padding-left:0px" class="hd">                        <span class="glyphicon glyphicon-play"></span><a href="Specification.html">Specification</a>                    </div>                    <div class="bd">                            <ul>                <li data-name="namespace:Specification_Middleware" class="opened">                    <div style="padding-left:18px" class="hd">                        <span class="glyphicon glyphicon-play"></span><a href="Specification/Middleware.html">Middleware</a>                    </div>                    <div class="bd">                            <ul>                <li data-name="class:Specification_Middleware_Slim" >                    <div style="padding-left:44px" class="hd leaf">                        <a href="Specification/Middleware/Slim.html">Slim</a>                    </div>                </li>                </ul></div>                </li>                            <li data-name="namespace:Specification_Service" class="opened">                    <div style="padding-left:18px" class="hd">                        <span class="glyphicon glyphicon-play"></span><a href="Specification/Service.html">Service</a>                    </div>                    <div class="bd">                            <ul>                <li data-name="class:Specification_Service_Exception" >                    <div style="padding-left:44px" class="hd leaf">                        <a href="Specification/Service/Exception.html">Exception</a>                    </div>                </li>                </ul></div>                </li>                            <li data-name="class:Specification_Controller" class="opened">                    <div style="padding-left:26px" class="hd leaf">                        <a href="Specification/Controller.html">Controller</a>                    </div>                </li>                            <li data-name="class:Specification_Exception" class="opened">                    <div style="padding-left:26px" class="hd leaf">                        <a href="Specification/Exception.html">Exception</a>                    </div>                </li>                            <li data-name="class:Specification_Service" class="opened">                    <div style="padding-left:26px" class="hd leaf">                        <a href="Specification/Service.html">Service</a>                    </div>                </li>                </ul></div>                </li>                </ul>';

    var searchTypeClasses = {
        'Namespace': 'label-default',
        'Class': 'label-info',
        'Interface': 'label-primary',
        'Trait': 'label-success',
        'Method': 'label-danger',
        '_': 'label-warning'
    };

    var searchIndex = [
                    {"type": "Namespace", "link": "Specification.html", "name": "Specification", "doc": "Namespace Specification"},{"type": "Namespace", "link": "Specification/Middleware.html", "name": "Specification\\Middleware", "doc": "Namespace Specification\\Middleware"},{"type": "Namespace", "link": "Specification/Service.html", "name": "Specification\\Service", "doc": "Namespace Specification\\Service"},
            
            {"type": "Class", "fromName": "Specification", "fromLink": "Specification.html", "link": "Specification/Controller.html", "name": "Specification\\Controller", "doc": "&quot;\n&quot;"},
                                                        {"type": "Method", "fromName": "Specification\\Controller", "fromLink": "Specification/Controller.html", "link": "Specification/Controller.html#method___construct", "name": "Specification\\Controller::__construct", "doc": "&quot;\n&quot;"},
                    {"type": "Method", "fromName": "Specification\\Controller", "fromLink": "Specification/Controller.html", "link": "Specification/Controller.html#method_loadFromClassesArray", "name": "Specification\\Controller::loadFromClassesArray", "doc": "&quot;\n&quot;"},
                    {"type": "Method", "fromName": "Specification\\Controller", "fromLink": "Specification/Controller.html", "link": "Specification/Controller.html#method_loadFromNamespacesPrefix", "name": "Specification\\Controller::loadFromNamespacesPrefix", "doc": "&quot;\n&quot;"},
                    {"type": "Method", "fromName": "Specification\\Controller", "fromLink": "Specification/Controller.html", "link": "Specification/Controller.html#method_parseClass", "name": "Specification\\Controller::parseClass", "doc": "&quot;\n&quot;"},
                    {"type": "Method", "fromName": "Specification\\Controller", "fromLink": "Specification/Controller.html", "link": "Specification/Controller.html#method_getSpecifications", "name": "Specification\\Controller::getSpecifications", "doc": "&quot;\n&quot;"},
                    {"type": "Method", "fromName": "Specification\\Controller", "fromLink": "Specification/Controller.html", "link": "Specification/Controller.html#method_getServiceSpecificationMap", "name": "Specification\\Controller::getServiceSpecificationMap", "doc": "&quot;\n&quot;"},
                    {"type": "Method", "fromName": "Specification\\Controller", "fromLink": "Specification/Controller.html", "link": "Specification/Controller.html#method_parseSchema", "name": "Specification\\Controller::parseSchema", "doc": "&quot;Parse schema - populate with default values if they are not specified.&quot;"},
                    {"type": "Method", "fromName": "Specification\\Controller", "fromLink": "Specification/Controller.html", "link": "Specification/Controller.html#method_decodeDataWithSchemaArray", "name": "Specification\\Controller::decodeDataWithSchemaArray", "doc": "&quot;\n&quot;"},
                    {"type": "Method", "fromName": "Specification\\Controller", "fromLink": "Specification/Controller.html", "link": "Specification/Controller.html#method_decodeDataWithSchemaFile", "name": "Specification\\Controller::decodeDataWithSchemaFile", "doc": "&quot;\n&quot;"},
                    {"type": "Method", "fromName": "Specification\\Controller", "fromLink": "Specification/Controller.html", "link": "Specification/Controller.html#method_validateDataWithSchemaFile", "name": "Specification\\Controller::validateDataWithSchemaFile", "doc": "&quot;\n&quot;"},
                    {"type": "Method", "fromName": "Specification\\Controller", "fromLink": "Specification/Controller.html", "link": "Specification/Controller.html#method_validateDataWithSchemaArray", "name": "Specification\\Controller::validateDataWithSchemaArray", "doc": "&quot;\n&quot;"},
            
            {"type": "Class", "fromName": "Specification", "fromLink": "Specification.html", "link": "Specification/Exception.html", "name": "Specification\\Exception", "doc": "&quot;\n&quot;"},
                                                        {"type": "Method", "fromName": "Specification\\Exception", "fromLink": "Specification/Exception.html", "link": "Specification/Exception.html#method___construct", "name": "Specification\\Exception::__construct", "doc": "&quot;\n&quot;"},
                    {"type": "Method", "fromName": "Specification\\Exception", "fromLink": "Specification/Exception.html", "link": "Specification/Exception.html#method_getType", "name": "Specification\\Exception::getType", "doc": "&quot;\n&quot;"},
                    {"type": "Method", "fromName": "Specification\\Exception", "fromLink": "Specification/Exception.html", "link": "Specification/Exception.html#method_getDescription", "name": "Specification\\Exception::getDescription", "doc": "&quot;\n&quot;"},
            
            {"type": "Class", "fromName": "Specification\\Middleware", "fromLink": "Specification/Middleware.html", "link": "Specification/Middleware/Slim.html", "name": "Specification\\Middleware\\Slim", "doc": "&quot;\n&quot;"},
                                                        {"type": "Method", "fromName": "Specification\\Middleware\\Slim", "fromLink": "Specification/Middleware/Slim.html", "link": "Specification/Middleware/Slim.html#method___construct", "name": "Specification\\Middleware\\Slim::__construct", "doc": "&quot;\n&quot;"},
                    {"type": "Method", "fromName": "Specification\\Middleware\\Slim", "fromLink": "Specification/Middleware/Slim.html", "link": "Specification/Middleware/Slim.html#method_call", "name": "Specification\\Middleware\\Slim::call", "doc": "&quot;\n&quot;"},
                    {"type": "Method", "fromName": "Specification\\Middleware\\Slim", "fromLink": "Specification/Middleware/Slim.html", "link": "Specification/Middleware/Slim.html#method_forwardFrameworkResponse", "name": "Specification\\Middleware\\Slim::forwardFrameworkResponse", "doc": "&quot;\n&quot;"},
                    {"type": "Method", "fromName": "Specification\\Middleware\\Slim", "fromLink": "Specification/Middleware/Slim.html", "link": "Specification/Middleware/Slim.html#method_convertExceptionToResponse", "name": "Specification\\Middleware\\Slim::convertExceptionToResponse", "doc": "&quot;\n&quot;"},
                    {"type": "Method", "fromName": "Specification\\Middleware\\Slim", "fromLink": "Specification/Middleware/Slim.html", "link": "Specification/Middleware/Slim.html#method_onBeforeRouter", "name": "Specification\\Middleware\\Slim::onBeforeRouter", "doc": "&quot;\n&quot;"},
                    {"type": "Method", "fromName": "Specification\\Middleware\\Slim", "fromLink": "Specification/Middleware/Slim.html", "link": "Specification/Middleware/Slim.html#method_onBeforeDispatch", "name": "Specification\\Middleware\\Slim::onBeforeDispatch", "doc": "&quot;\n&quot;"},
                    {"type": "Method", "fromName": "Specification\\Middleware\\Slim", "fromLink": "Specification/Middleware/Slim.html", "link": "Specification/Middleware/Slim.html#method_onAfterRouter", "name": "Specification\\Middleware\\Slim::onAfterRouter", "doc": "&quot;\n&quot;"},
                    {"type": "Method", "fromName": "Specification\\Middleware\\Slim", "fromLink": "Specification/Middleware/Slim.html", "link": "Specification/Middleware/Slim.html#method_mount", "name": "Specification\\Middleware\\Slim::mount", "doc": "&quot;\n&quot;"},
                    {"type": "Method", "fromName": "Specification\\Middleware\\Slim", "fromLink": "Specification/Middleware/Slim.html", "link": "Specification/Middleware/Slim.html#method_getCurrentRouteDetails", "name": "Specification\\Middleware\\Slim::getCurrentRouteDetails", "doc": "&quot;\n&quot;"},
                    {"type": "Method", "fromName": "Specification\\Middleware\\Slim", "fromLink": "Specification/Middleware/Slim.html", "link": "Specification/Middleware/Slim.html#method_decodeRequest", "name": "Specification\\Middleware\\Slim::decodeRequest", "doc": "&quot;\n&quot;"},
                    {"type": "Method", "fromName": "Specification\\Middleware\\Slim", "fromLink": "Specification/Middleware/Slim.html", "link": "Specification/Middleware/Slim.html#method_decodeRequestParameters", "name": "Specification\\Middleware\\Slim::decodeRequestParameters", "doc": "&quot;\n&quot;"},
                    {"type": "Method", "fromName": "Specification\\Middleware\\Slim", "fromLink": "Specification/Middleware/Slim.html", "link": "Specification/Middleware/Slim.html#method_decodeRequestBody", "name": "Specification\\Middleware\\Slim::decodeRequestBody", "doc": "&quot;\n&quot;"},
                    {"type": "Method", "fromName": "Specification\\Middleware\\Slim", "fromLink": "Specification/Middleware/Slim.html", "link": "Specification/Middleware/Slim.html#method_validateRequest", "name": "Specification\\Middleware\\Slim::validateRequest", "doc": "&quot;Validates current request according to the specification.&quot;"},
                    {"type": "Method", "fromName": "Specification\\Middleware\\Slim", "fromLink": "Specification/Middleware/Slim.html", "link": "Specification/Middleware/Slim.html#method_validateWithSpecSchema", "name": "Specification\\Middleware\\Slim::validateWithSpecSchema", "doc": "&quot;\n&quot;"},
                    {"type": "Method", "fromName": "Specification\\Middleware\\Slim", "fromLink": "Specification/Middleware/Slim.html", "link": "Specification/Middleware/Slim.html#method_processRequest", "name": "Specification\\Middleware\\Slim::processRequest", "doc": "&quot;\n&quot;"},
                    {"type": "Method", "fromName": "Specification\\Middleware\\Slim", "fromLink": "Specification/Middleware/Slim.html", "link": "Specification/Middleware/Slim.html#method_validateResponse", "name": "Specification\\Middleware\\Slim::validateResponse", "doc": "&quot;\n&quot;"},
                    {"type": "Method", "fromName": "Specification\\Middleware\\Slim", "fromLink": "Specification/Middleware/Slim.html", "link": "Specification/Middleware/Slim.html#method_sanitizeResponse", "name": "Specification\\Middleware\\Slim::sanitizeResponse", "doc": "&quot;\n&quot;"},
                    {"type": "Method", "fromName": "Specification\\Middleware\\Slim", "fromLink": "Specification/Middleware/Slim.html", "link": "Specification/Middleware/Slim.html#method_prepareResponse", "name": "Specification\\Middleware\\Slim::prepareResponse", "doc": "&quot;\n&quot;"},
                    {"type": "Method", "fromName": "Specification\\Middleware\\Slim", "fromLink": "Specification/Middleware/Slim.html", "link": "Specification/Middleware/Slim.html#method_formatResponse", "name": "Specification\\Middleware\\Slim::formatResponse", "doc": "&quot;\n&quot;"},
                    {"type": "Method", "fromName": "Specification\\Middleware\\Slim", "fromLink": "Specification/Middleware/Slim.html", "link": "Specification/Middleware/Slim.html#method_getDefaultResponse", "name": "Specification\\Middleware\\Slim::getDefaultResponse", "doc": "&quot;\n&quot;"},
            
            {"type": "Class", "fromName": "Specification", "fromLink": "Specification.html", "link": "Specification/Service.html", "name": "Specification\\Service", "doc": "&quot;Specification service&quot;"},
                                                        {"type": "Method", "fromName": "Specification\\Service", "fromLink": "Specification/Service.html", "link": "Specification/Service.html#method_execute", "name": "Specification\\Service::execute", "doc": "&quot;This method gets executed when router selects its matching side.&quot;"},
            
            {"type": "Class", "fromName": "Specification\\Service", "fromLink": "Specification/Service.html", "link": "Specification/Service/Exception.html", "name": "Specification\\Service\\Exception", "doc": "&quot;\n&quot;"},
                                                        {"type": "Method", "fromName": "Specification\\Service\\Exception", "fromLink": "Specification/Service/Exception.html", "link": "Specification/Service/Exception.html#method___construct", "name": "Specification\\Service\\Exception::__construct", "doc": "&quot;\n&quot;"},
            
            
                                        // Fix trailing commas in the index
        {}
    ];

    /** Tokenizes strings by namespaces and functions */
    function tokenizer(term) {
        if (!term) {
            return [];
        }

        var tokens = [term];
        var meth = term.indexOf('::');

        // Split tokens into methods if "::" is found.
        if (meth > -1) {
            tokens.push(term.substr(meth + 2));
            term = term.substr(0, meth - 2);
        }

        // Split by namespace or fake namespace.
        if (term.indexOf('\\') > -1) {
            tokens = tokens.concat(term.split('\\'));
        } else if (term.indexOf('_') > 0) {
            tokens = tokens.concat(term.split('_'));
        }

        // Merge in splitting the string by case and return
        tokens = tokens.concat(term.match(/(([A-Z]?[^A-Z]*)|([a-z]?[^a-z]*))/g).slice(0,-1));

        return tokens;
    };

    root.Sami = {
        /**
         * Cleans the provided term. If no term is provided, then one is
         * grabbed from the query string "search" parameter.
         */
        cleanSearchTerm: function(term) {
            // Grab from the query string
            if (typeof term === 'undefined') {
                var name = 'search';
                var regex = new RegExp("[\\?&]" + name + "=([^&#]*)");
                var results = regex.exec(location.search);
                if (results === null) {
                    return null;
                }
                term = decodeURIComponent(results[1].replace(/\+/g, " "));
            }

            return term.replace(/<(?:.|\n)*?>/gm, '');
        },

        /** Searches through the index for a given term */
        search: function(term) {
            // Create a new search index if needed
            if (!bhIndex) {
                bhIndex = new Bloodhound({
                    limit: 500,
                    local: searchIndex,
                    datumTokenizer: function (d) {
                        return tokenizer(d.name);
                    },
                    queryTokenizer: Bloodhound.tokenizers.whitespace
                });
                bhIndex.initialize();
            }

            results = [];
            bhIndex.get(term, function(matches) {
                results = matches;
            });

            if (!rootPath) {
                return results;
            }

            // Fix the element links based on the current page depth.
            return $.map(results, function(ele) {
                if (ele.link.indexOf('..') > -1) {
                    return ele;
                }
                ele.link = rootPath + ele.link;
                if (ele.fromLink) {
                    ele.fromLink = rootPath + ele.fromLink;
                }
                return ele;
            });
        },

        /** Get a search class for a specific type */
        getSearchClass: function(type) {
            return searchTypeClasses[type] || searchTypeClasses['_'];
        },

        /** Add the left-nav tree to the site */
        injectApiTree: function(ele) {
            ele.html(treeHtml);
        }
    };

    $(function() {
        // Modify the HTML to work correctly based on the current depth
        rootPath = $('body').attr('data-root-path');
        treeHtml = treeHtml.replace(/href="/g, 'href="' + rootPath);
        Sami.injectApiTree($('#api-tree'));
    });

    return root.Sami;
})(window);

$(function() {

    // Enable the version switcher
    $('#version-switcher').change(function() {
        window.location = $(this).val()
    });

    
        // Toggle left-nav divs on click
        $('#api-tree .hd span').click(function() {
            $(this).parent().parent().toggleClass('opened');
        });

        // Expand the parent namespaces of the current page.
        var expected = $('body').attr('data-name');

        if (expected) {
            // Open the currently selected node and its parents.
            var container = $('#api-tree');
            var node = $('#api-tree li[data-name="' + expected + '"]');
            // Node might not be found when simulating namespaces
            if (node.length > 0) {
                node.addClass('active').addClass('opened');
                node.parents('li').addClass('opened');
                var scrollPos = node.offset().top - container.offset().top + container.scrollTop();
                // Position the item nearer to the top of the screen.
                scrollPos -= 200;
                container.scrollTop(scrollPos);
            }
        }

    
    
        var form = $('#search-form .typeahead');
        form.typeahead({
            hint: true,
            highlight: true,
            minLength: 1
        }, {
            name: 'search',
            displayKey: 'name',
            source: function (q, cb) {
                cb(Sami.search(q));
            }
        });

        // The selection is direct-linked when the user selects a suggestion.
        form.on('typeahead:selected', function(e, suggestion) {
            window.location = suggestion.link;
        });

        // The form is submitted when the user hits enter.
        form.keypress(function (e) {
            if (e.which == 13) {
                $('#search-form').submit();
                return true;
            }
        });

    
});


