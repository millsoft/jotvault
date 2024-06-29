import BackendApi from "@/utils/BackendApi";

export async function GET(
    request: Request,
    {params}: { params: { id: string } }
) {

    console.log("COOL_HERE");
    const res = await new BackendApi().fetch(`/api/notes/${params.id}`, {
        next: {revalidate: 10}
    });

    const data = await res.json()
    return Response.json(data)
}
