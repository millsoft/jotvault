/**
 * BackendApi class to make API calls to the JotVault API
 */
export default class BackendApi {

    getEndpointUrl(path?: string): string {
        const url = new URL(path, process.env.API_BACKEND_URL);
        return url.toString();
    }

    async fetch(path: string, init?: RequestInit): Promise<Response> {
        const url = this.getEndpointUrl(path);
        console.log("BACKEND_URL", url);
        return await fetch(url, init);
    }

}
