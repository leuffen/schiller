import {KaCustomFragment, template} from "@kasimirjs/embed";

// language=html
let html = `

<div class="row">
    <div class="col-lg-4">
        <div class="input-group mb-3">
            <label class="input-group-text" for="alt1"><b>Title:</b></label>
            <input class="form-control" placeholder="Bitte alt-text eingeben" ka.bind="$scope.alt" id="alt1">
        </div>
        <div ka.for="let curVariant of media.variant" class="row">
            <div ka.if="curVariant.variantId.startsWith('s.')" class="input-group mb-3">
                <label class="input-group-text w-50" for="inputGroupSelect01"><b>[[ curVariant.variantId ]]</b>&nbsp; ([[ curVariant.width + "x" + curVariant.height ]])</label>
                <select class="form-select w-50" value="include" ka.bind="$scope.builder[curVariant.variantId]" ka.options="{'': '', 'always': 'Für alle Größen nutzen' ,'include': 'Nutzen', 'hide': 'Kleiner verstecken'}"id="inputGroupSelect01">
                   
                </select>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <textarea class="h-100 w-100 overflow-auto form-control"  wrap="off" readonly ka.bind="output"></textarea>
    </div>
</div>

`
@template(html)
export class ImageBuilderTab extends KaCustomFragment {

    constructor() {
        super();

        let scope = this.init({
            builder: {},
            output: "Bitte einzubindene Bilder wählen",
            alt: "",

            $on: {
                change: () => {
                    let code = `<picture title="${scope.alt}">\n`;
                    for (let id in scope.builder) {
                        let image = scope.media.variant.find((v) => v.variantId === id);
                        if (scope.builder[id] === "")
                            continue
                        if (scope.builder[id] === "hide") {
                            code += `  <source media="(max-width: ${image.width}px)" srcset="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" height="0" width="0"/>\n`
                            continue;
                        }

                        for (let ext of image.extensions) {
                            let minWidth = `(min-width: ${image.width}px)`;
                            if (scope.builder[id] === 'always')
                                minWidth = "";
                            code += `  <source media="${minWidth}" srcset="${scope.index.baseUrl}/${image.url}.${ext}" height="${image.height}" width="${image.width}"/>\n`;
                        }
                    }
                    code += "</picture>\n";
                    scope.output = code;
                }
            }
        });
    }

}
