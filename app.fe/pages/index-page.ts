import {customElement, ka_sleep, KaCustomElement, KaHtmlElement, template} from "@kasimirjs/embed";
import {href, link, route, router} from "@kasimirjs/app";
import {currentRoute} from "@kasimirjs/app";
import {CurRoute} from "@kasimirjs/app";
import {ImageDetailsModal} from "../modals/image-details-modal";

// language=html
let html = `
        
<div class="container-xxl">
    <div class="row">
        <h2>Bitte Scope wählen:</h2>
        <ul>
            <li ka.for="let scope of index.scopes"><a ka.attr.href="$fn.getLink(scope)">[[ scope ]]</a></li>
        </ul>
    </div>
    
</div>


`

@customElement("index-page")
@route("gallery", "/static/{subscription_id}")
@template(html)
class IndexPage extends KaCustomElement {

    constructor(public route : CurRoute) {
        super();
        let scope = this.init({
            $fn: {
                getLink: (scope) => href("gallery", {scope_id: scope})
            }
        })
    }

    async connectedCallback(): Promise<void> {


        let subId = currentRoute.route_params["subscription_id"];


        this.scope.index = await (await fetch("/v1/api/" + subId + "/info")).json();
        super.connectedCallback();
        this.scope.render();

    }


    // language=html

}
