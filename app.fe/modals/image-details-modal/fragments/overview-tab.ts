import {ka_sleep, KaCustomFragment, template} from "@kasimirjs/embed";
import {KitFormInput} from "@kasimirjs/kit-bootstrap";

// language=html
let html = `


<div class="mb-3">
    <h4>CDN Link kopieren:</h4> 
    <div class="input-group mb-3">
        <div class="input-group mb-3">
            <label class="input-group-text" for="inputGroupSelect01">Größe wählen:</label>
            <input class="form-control" readonly ka.bind="$scope.cdnLink">
        </div>
    </div>
    
    
    <div class="row bg-light">
        <div class="col-md-1 d-none d-md-block">
            <span class="display-1">1.</span>
        </div>
        <div class="col-md-11 pt-3">
            <h4>Passende Bildgröße auswählen: <span ka.if="selectedImage !== null" class="badge bg-primary small">[[ selectedImage.width + "x" + selectedImage.height ]]</span></h4> 
            <div class="input-group mb-3">
                <div class="input-group mb-3">
                    <label class="input-group-text" for="inputGroupSelect01">Größe wählen:</label>
                    <select class="form-select" value="include" ka.bind="$scope.selectedVariant" ka.options="media.variant.map((e) => e.variantId)" id="inputGroupSelect01">
                    </select>
                </div>
            </div>
        </div>
    </div>
   
    <div class="row bg-light" ka.if="selectedVariant !== null">
        <div class="col-md-1 d-none d-md-block">
            <span class="display-1">2.</span>
        </div>
        <div class="col-md-11 pt-3">
            <h4>CDN Short-Link kopieren: <span class="text-muted fs-6">für die Verwendung in <code>data-leu-src=</code> und <code>data-leu-bg-src=</code> Attributen</span></h4> 
            <div class="input-group mb-3">
                <div class="input-group mb-3">
                    <input type="url" ka.prop.value="$fn.getCdnUrl()" readonly class="form-control">
                    
                </div>
            </div>
        </div>
    </div>

    <div class="row bg-light" ka.if="selectedVariant !== null">
        <div class="col-md-1 d-none d-md-block">
            <span class="display-1">3.</span>
        </div>
        <div class="col-md-11 pt-3">
            <h4>Reguläre Bildurl laden:</h4>
            <div class="input-group mb-3">
                <label class="input-group-text" for="inputGroupSelect01">Format:</label>
                <select ka.if="selectedVariant !== null"  class="form-select w-auto" value="include" ka.bind="$scope.selectedExtension" ka.options="media.variant.find((e) => e.variantId === selectedVariant).extensions" id="inputGroupSelect01">
                </select>
                <input type="url" ka.bind="$scope.selectedUrl" readonly class="form-control">
            </div>
        </div>
    </div>
    <div class="input-group mb-3" ka.if="selectedImage !== null">
        
        
    </div>

    <div ka.if="selectedImage !== null">
        
        <div class="ratio-1x1 ratio border-primary bg-karo d-flex w-50">
            <a ka.attr.href="$scope.selectedUrl" class="w-100 h-100 d-flex" target="_blank"><img class="h-auto h-auto my-auto mx-auto" style="max-width: 100%; max-height: 100%" ka.attr.src="$scope.selectedUrl"></a>
        </div>
    </div>
    
       
</div>
`;


@template(html)
export class ImageDetailsOverviewFragment extends KaCustomFragment {

    constructor() {
        super();

        let scope = this.init({
            cdnLink: "updating",
            selectedVariant: null,
            selectedExtension: null,
            selectedImage: null,
            selectUrl:null,
            forms: [
                new KitFormInput("$scope.media.name", {title: "Dateiname", readonly: true}),
                new KitFormInput("$scope.media.license", {title: "Lizenz", readonly: true})
            ],
            code: "Bitte wählen",
            $fn: {
                getCdnUrl: () => {
                    let variant = scope.media.variant.filter(v => v.variantId === scope.selectedVariant)[0];
                    return `${scope.index.baseUrl}/${variant.url}.(` + variant.extensions.join(",") + ")";
                },
                getCdnGlobUrl: (media) => {
                    let variants = this.scope.media.variant.filter(x => x.variantId !== "preview").map((x) => x.width + "x" + x.height).join("_");
                    let extensions = this.scope.media.variant.filter(x => x.variantId !== "preview")[0].extensions.join("_");
                    return `cdn:///${this.scope.index.scope}/v/${this.scope.media.id}/${variants}/${this.scope.media.name}.${extensions}`;
                }
            },
            $on: {
                change: () => {
                    console.log(scope.dump());
                    let variant = scope.media.variant.filter(v => v.variantId === scope.selectedVariant)[0];
                    scope.selectedImage = variant;
                    let code = variant.extensions.map(e => scope.index.baseUrl + "/" + variant.url + "." + e);
                    scope.code = code.join("\n");
                    scope.selectedUrl = scope.index.baseUrl + variant.url + "." + scope.selectedExtension;
                }
            }
        })
        //
    }

    async fragmentConnectedCallback(parentElement: HTMLElement): Promise<void> {
        await super.fragmentConnectedCallback(parentElement);
        this.scope.cdnLink = this.scope.$fn.getCdnGlobUrl();
    }

}
