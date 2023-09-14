import {customElement, ka_sleep, KaCustomElement, KaHtmlElement, template} from "@kasimirjs/embed";
import {messageBus, route, router} from "@kasimirjs/app";
import {currentRoute} from "@kasimirjs/app";
import {CurRoute} from "@kasimirjs/app";
import {ImageDetailsModal} from "../modals/image-details-modal";

import {IndexUpdatedMessage} from "../messages/index-updated-message";

// language=html
let html = `
        
<div class="container-fluid" style="margin-bottom: 200px;user-select: none">
    <div class="row" ka.if="index !== null" >
        <div class="col-2 m-0 p-1" ka.for="let curMedia of index.media" style="min-width: 200px;max-width: 212px;">
            <div class="card m-0" ka.classlist.border-primary="$scope.selected.includes(curMedia.id)" ka.classlist.shadow="$scope.selected.includes(curMedia.id)">
                <div class="card-body position-relative p-1" ka.on.click="$fn.select(curMedia, $event)">
                    
                    <div ka.classlist.bg-karo="curMedia.type !== 'download'" class=" rounded">
                        <div ka.if="curMedia.type !== 'download'" class="ratio ratio-1x1 text-center " style="background-size: cover;background-repeat:no-repeat; background-position: center center">
                            <img loading="lazy" ka.attr.src="index.baseUrl + curMedia.previewUrl">
                        </div>
                        <div ka.if="curMedia.type === 'download'" class="ratio ratio-1x1 text-center ">
                           <div class="mt-4">[[ curMedia.name + "." + curMedia.extension ]]</div> 
                        </div>
                    </div>
                    
                    <span class="position-absolute top-0 start-0 badge bg-primary bg-opacity-50 m-1">[[curMedia.type]]</span>
                    <span class="position-absolute top-0 end-0 badge bg-secondary bg-opacity-50 m-1">[[ curMedia.info1 ]]</span>
                    
                    <div class="position-absolute badge bottom-0 end-0 badge bg-secondary bg-opacity-50 m-1">
                        <div class="dropdown">
                            <button class="btn btn-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-gear" viewBox="0 0 16 16">
                                    <path d="M8 4.754a3.246 3.246 0 1 0 0 6.492 3.246 3.246 0 0 0 0-6.492zM5.754 8a2.246 2.246 0 1 1 4.492 0 2.246 2.246 0 0 1-4.492 0z"/>
                                    <path d="M9.796 1.343c-.527-1.79-3.065-1.79-3.592 0l-.094.319a.873.873 0 0 1-1.255.52l-.292-.16c-1.64-.892-3.433.902-2.54 2.541l.159.292a.873.873 0 0 1-.52 1.255l-.319.094c-1.79.527-1.79 3.065 0 3.592l.319.094a.873.873 0 0 1 .52 1.255l-.16.292c-.892 1.64.901 3.434 2.541 2.54l.292-.159a.873.873 0 0 1 1.255.52l.094.319c.527 1.79 3.065 1.79 3.592 0l.094-.319a.873.873 0 0 1 1.255-.52l.292.16c1.64.893 3.434-.902 2.54-2.541l-.159-.292a.873.873 0 0 1 .52-1.255l.319-.094c1.79-.527 1.79-3.065 0-3.592l-.319-.094a.873.873 0 0 1-.52-1.255l.16-.292c.893-1.64-.902-3.433-2.541-2.54l-.292.159a.873.873 0 0 1-1.255-.52l-.094-.319zm-2.633.283c.246-.835 1.428-.835 1.674 0l.094.319a1.873 1.873 0 0 0 2.693 1.115l.291-.16c.764-.415 1.6.42 1.184 1.185l-.159.292a1.873 1.873 0 0 0 1.116 2.692l.318.094c.835.246.835 1.428 0 1.674l-.319.094a1.873 1.873 0 0 0-1.115 2.693l.16.291c.415.764-.42 1.6-1.185 1.184l-.291-.159a1.873 1.873 0 0 0-2.693 1.116l-.094.318c-.246.835-1.428.835-1.674 0l-.094-.319a1.873 1.873 0 0 0-2.692-1.115l-.292.16c-.764.415-1.6-.42-1.184-1.185l.159-.291A1.873 1.873 0 0 0 1.945 8.93l-.319-.094c-.835-.246-.835-1.428 0-1.674l.319-.094A1.873 1.873 0 0 0 3.06 4.377l-.16-.292c-.415-.764.42-1.6 1.185-1.184l.292.159a1.873 1.873 0 0 0 2.692-1.115l.094-.319z"/>
                                </svg>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="javascript:void()" ka.on.click="$fn.details(curMedia)">Details</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                
            </div>
            
        </div>
    </div>
    
</div>
<nav class="navbar position-fixed bottom-0 w-100 bg-light border-top shadow-lg" style="user-select:none;">
    <div class="container-fluid">
        <div ka.if="selected.length === 0">
            <app-file-upload></app-file-upload>
        </div>
        <div ka.if="selected.length > 0" class="row w-100">
            
            <div ka.for="value of $fn.getCopyValues()" class="col">
                <label for="basic-url" class="form-label">[[ value.text ]]</label>
                <div class="input-group mb-3 w-100">
                    <input ka.ref="value.id" type="text" class="form-control bg-white" readonly ka.attr.value="value.val" ka.on.click="$fn.copyClipboard($ref.flexUrl)" id="basic-url" aria-describedby="basic-addon3">
                    <button class="btn btn-primary" id="basic-addon3" ka.on.click="$fn.copyClipboard($ref[value.id])">Copy</button>
                </div>
            </div>
     
        </div>
    </div>


</nav>


`

@customElement("gallery-page")
@route("gallery", "/static/{subscription_id}/{scope_id}")
@template(html)
class GalleryPage extends KaCustomElement {

    constructor(public route : CurRoute) {
        super();
        let scope = this.init({
            index: null,
            selected: [],
            $fn: {
                details: (media) => (new ImageDetailsModal()).show(scope.index, media),
                select: (media, $event : PointerEvent) => {
                    if ($event.shiftKey || $event.ctrlKey) {
                        if (scope.selected.includes(media.id)) {
                            scope.selected = scope.selected.filter(id => id != media.id);
                        } else {
                            scope.selected = [...scope.selected, media.id];
                        }
                    } else {
                        if (scope.selected.includes(media.id)) {
                            scope.selected = [];
                        } else {
                            scope.selected = [media.id];
                        }
                    }
                },
                getImageFlexUrl() {
                    let media = scope.index.media.filter(media => media.id === scope.selected[0])[0];
                    return scope.index.baseUrl + media.previewUrl;
                },
                getEmbedMd() {
                    let media = scope.index.media.filter(media => media.id === scope.selected[0])[0];
                    let url = scope.$fn.getImageFlexUrl();
                    return `![${media.userDescription}](${url})`;
                },
                copyClipboard(element : HTMLInputElement) {
                    element.select();
                    navigator.clipboard.writeText(element.value);
                    //document.execCommand('copy');
                },
                getCopyValues() {
                    let media = scope.index.media.filter(media => media.id === scope.selected[0])[0];
                    let values = [];
                    if (media.type === "image" || media.type === "svg") {
                        values.push({id: "flexUrl", text: "Flex Url", val: scope.$fn.getImageFlexUrl()});
                        values.push({id: "embedMd", text: "Embed Markdown", val: scope.$fn.getEmbedMd()});
                    } else if (media.type === "pdf") {
                        values.push({id: "flexUrl", text: "PDF Download Url", val: scope.index.baseUrl + media.origUrl});
                        values.push({id: "PreviewUrl", text: "PDF Preview Url", val: scope.index.baseUrl + media.previewUrl});
                    } else {
                        values.push({id: "downloadUrl", text: "Download Url", val: scope.index.baseUrl + media.origUrl});
                    }
                    return values;
                }
            }
        })
    }

    async connectedCallback(): Promise<void> {
        super.connectedCallback();

        let subId = currentRoute.route_params["subscription_id"];
        let scopeId = currentRoute.route_params["scope_id"];

        messageBus.on(IndexUpdatedMessage, async () => {
            this.scope.importFrom({
                index: await (await fetch("/v1/api/" + subId + "/" + scopeId + "/index.json")).json(),
            });
            this.scope.render();
        })

        messageBus.trigger(new IndexUpdatedMessage())

    }


    // language=html

}
