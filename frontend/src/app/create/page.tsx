"use client"
import {ChangeEvent, useState} from "react";
import {saveNote} from "./actions";

interface IFormData {
    title: string;
    content: string;
    password: string;
}

export default function Page() {

    // State with TypeScript types
    const [formData, setFormData] = useState<IFormData>({
        title: 'New Note',
        content: '',
        password: ''
    });

    // Handle input changes with TypeScript types for event
    const handleChange = (event: ChangeEvent<HTMLInputElement | HTMLTextAreaElement>) => {
        const {name, value} = event.target;
        setFormData(prevState => ({
            ...prevState,
            [name]: value
        }));
    };

    const submitNote = async (event: React.FormEvent) => {
        event.preventDefault();

        const X_SESSION_ID = 'X-Session-Id';
        const X_ENCRYPTION_KEY = 'X-Session-Id';

        // get the X-Session-Id from the local storage:
        const sessionId = localStorage.getItem(X_SESSION_ID);
        // Send form data to the server
        const ENDPOINT = 'http://localhost:8000/api/notes';
        const response = await fetch(ENDPOINT, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/ld+json',
                [X_ENCRYPTION_KEY]: formData.password,
                [X_SESSION_ID]: sessionId
            },
            body: JSON.stringify({
                title: formData.title,
                content: formData.content
            })
        });

        // get the X-Session-Id from the response headers
        localStorage.setItem('X-Session-Id', response.headers.get('X-Session-Id'));

        //get the ID from the response and redirect to the note page
        const data = await response.json();
        window.location.href = `/n/${data.id}`;

    }

    return (
        <main className="flex min-h-screen flex-col items-center justify-center p-24 bg-gray-100">
            <div className="z-10 w-full max-w-5xl flex flex-col items-center justify-between font-mono text-sm">
                <h1 className="text-4xl font-bold text-gray-800 mb-8">Add New Note</h1>
                <form className="space-y-4 w-full max-w-md" action={saveNote}>
                    <div>
                        <label htmlFor="title" className="form-label">Title</label>
                        <input
                            type="text"
                            id="title"
                            name="title"
                            value={formData.title}
                            className="form-input"
                        />
                    </div>
                    <div>
                        <label htmlFor="content" className="form-label">Content</label>
                        <textarea
                            id="content"
                            name="content"
                            value={formData.content}
                            onChange={handleChange}
                            className="form-input"
                            rows={10}
                        />
                    </div>
                    <div>
                        <label htmlFor="password" className="form-label">Password</label>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            value={formData.password}
                            onChange={handleChange}
                            className="form-input"
                        />
                    </div>
                    <button type="submit" className="form-button">
                        Add Note
                    </button>
                </form>
            </div>
        </main>
    );
}
