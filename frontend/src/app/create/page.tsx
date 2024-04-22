"use client"
import {ChangeEvent, useState} from "react";

interface FormData {
    title: string;
    content: string;
    password: string;
}

export default function Page() {

    // State with TypeScript types
    const [formData, setFormData] = useState<FormData>({
        title: 'New Note',
        content: '',
        password: ''
    });

    // Handle input changes with TypeScript types for event
    const handleChange = (event: ChangeEvent<HTMLInputElement | HTMLTextAreaElement>) => {
        const { name, value } = event.target;
        setFormData(prevState => ({
            ...prevState,
            [name]: value
        }));
    };

    const submitNote = async (event: React.FormEvent) => {
        event.preventDefault();

        // Send form data to the server
        const ENDPOINT = 'http://localhost:8000/api/notes';
        //const ENDPOINT = 'https://de33-95-223-57-208.ngrok-free.app/api/notes';
        const response = await fetch(ENDPOINT, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Encryption-Key': formData.password
            },
            body: JSON.stringify({
                title: formData.title,
                content: formData.content
            })
        });

    }

    return (
        <main className="flex min-h-screen flex-col items-center justify-center p-24 bg-gray-100">
            <div className="z-10 w-full max-w-5xl flex flex-col items-center justify-between font-mono text-sm">
                <h1 className="text-4xl font-bold text-gray-800 mb-8">Add New Note</h1>
                <form className="space-y-4 w-full max-w-md" onSubmit={submitNote}>
                    <div>
                        <label htmlFor="title" className="form-label">Title</label>
                        <input
                            type="text"
                            id="title"
                            name="title"
                            value={formData.title}
                            onChange={handleChange}
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
