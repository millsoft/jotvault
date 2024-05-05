import Link from "next/link";

const fetchNote = async (id: string): Promise<{
    title: string,
    content: string
}> => {
    const baseUrl = process.env.NEXT_PUBLIC_BASE_URL || "";
    const response = await fetch(`${baseUrl}/api/note/${id}`);
    return await response.json();
}

export default async function Page(params: { params: { id: string } }) {
    const note = await fetchNote(params.params.id)

    return (
        <main className="flex min-h-screen flex-col items-center justify-center p-24 bg-gray-100">
            <div className="z-10 w-full max-w-5xl flex flex-col items-center justify-between font-mono text-sm">
                <h1 className="text-4xl font-bold text-gray-800 mb-8">{note.title}</h1>
                <div className={"text-black"}>
                    {note.content}
                </div>
                <Link className={"form-button"} href={"/create"}>CREATE NEW NOTE</Link>
            </div>

        </main>
    );
}
