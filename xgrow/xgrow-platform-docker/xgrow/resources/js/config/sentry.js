import * as Sentry from "@sentry/vue";
import {BrowserTracing} from "@sentry/tracing";
import {getCookie} from "../functions/helper";

/** SENTRY CONFIG
 * https://docs.sentry.io/platforms/javascript/guides/vue/
 */
const values = JSON.parse(getCookie("auth.uuid"));

export default function initSentry(vueApp, router){
    Sentry.init({
        vueApp,
        dsn: atob(values.dsn.substring(5)),
        integrations: [
            new BrowserTracing({
                routingInstrumentation: Sentry.vueRouterInstrumentation(router),
                tracingOrigins: ["*.xgrow.com.br", "*.xgrow.com", /^\//],
            }),
        ],
        tracesSampleRate: 0.2,
        environment: atob(values.env.substring(5)),
    });

}
