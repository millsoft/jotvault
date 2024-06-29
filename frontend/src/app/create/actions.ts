"use server";
import BackendApi from "@/utils/BackendApi";
import {redirect} from "next/navigation";

export const saveNote = async (formData: FormData) => {
    const X_SESSION_ID = 'X-Session-Id';
    const X_ENCRYPTION_KEY = 'X-Session-Id';

    // get the X-Session-Id from the local storage:
    //const sessionId = localStorage.getItem(X_SESSION_ID);
    // Send form data to the server
    const ENDPOINT = '/api/notes';

    const response = await new BackendApi().fetch(ENDPOINT, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/ld+json',
            [X_ENCRYPTION_KEY]: formData.get('password'),
            [X_SESSION_ID]: "NOPE"
        },
        body: JSON.stringify({
            title: formData.get('title'),
            content: formData.get('content')
        })
    });

    const data = await response.json();
    redirect(`/n/${data.id}`)
}
