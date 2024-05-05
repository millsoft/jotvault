export async function GET() {
    const res = await fetch('http://localhost:8000/api/notes/018f4485-2d46-7b37-8eb3-2ce972f8d395', {
        next: { revalidate: 10 }
    })
    const data = await res.json()

    return Response.json(data)
}
