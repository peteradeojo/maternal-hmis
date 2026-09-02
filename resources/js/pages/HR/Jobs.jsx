import { router, useForm, useHttp } from "@inertiajs/react";
import { useEffect, useState } from "react";
// import { triggerAlert } from "../../app";

export default function Jobs({ jobs }) {
    const defaultJobFields = {
        slug: "",
        title: "",
        summary: "",
        description: "",
        responsibilities: "",
        requirements: "",
        footer: "",
    };

    const { data, setData, post, patch } = useHttp({ ...defaultJobFields });

    const toggleJob = async (id) => {
        const data = await patch(`/api/job-openings/${id}`);
        console.log(data);
        window.notifySuccess("Job updated");
    }

    const createJob = (e) => {
        e.preventDefault();
        try {
            const data = post("/api/job-openings", {
                headers: {
                    Accept: "application/json",
                    "content-type": "application/json",
                },
            });
            alert("Job posting saved.");
            setData();
        } catch (error) {
            console.error(error);
            alert(error);
        }
    };

    useEffect(() => {
        setData(
            "slug",
            data.title
                .toLowerCase()
                .replaceAll(/[\(\)]/g, "")
                .replaceAll(/\s/g, "-"),
        );
    }, [data.title]);

    return (
        <>
            <div className="card">
                <form onSubmit={createJob} className="grid grid-cols-2 gap-x-4">
                    <div className="form-group">
                        <label>Slug</label>
                        <input
                            type="text"
                            className="form-control"
                            required
                            readOnly={true}
                            value={data.slug}
                            onChange={(e) => setData("slug", e.target.value)}
                        />
                    </div>
                    <div className="form-group">
                        <label>Title</label>
                        <input
                            type="text"
                            className="form-control"
                            required
                            value={data.title}
                            onChange={(e) => setData("title", e.target.value)}
                        />
                    </div>
                    <div className="form-group">
                        <label>Summary</label>
                        <textarea
                            required
                            className="form-control"
                            value={data.summary}
                            onChange={(e) => setData("summary", e.target.value)}
                        ></textarea>
                    </div>
                    <div className="form-group">
                        <label>Description</label>
                        <textarea
                            required
                            className="form-control"
                            value={data.description}
                            onChange={(e) =>
                                setData("description", e.target.value)
                            }
                        ></textarea>
                    </div>
                    <div className="form-group">
                        <label>Responsibilities</label>
                        <textarea
                            className="form-control"
                            value={data.responsibilities}
                            onChange={(e) =>
                                setData("responsibilities", e.target.value)
                            }
                        ></textarea>
                    </div>
                    <div className="form-group">
                        <label>Requirements</label>
                        <textarea
                            className="form-control"
                            value={data.requirements}
                            onChange={(e) =>
                                setData("requirements", e.target.value)
                            }
                        ></textarea>
                    </div>
                    <div className="form-group">
                        <label>Footer</label>
                        <textarea
                            className="form-control"
                            value={data.footer}
                            onChange={(e) => setData("footer", e.target.value)}
                        ></textarea>
                    </div>

                    <div className="form-group col-span-full">
                        <button className="btn bg-primary text-white">
                            Submit
                        </button>
                    </div>
                </form>
            </div>

            <div className="card">
                <table className="table">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        {jobs.map((job) => (
                            <tr>
                                <td>{job.title}</td>
                                <td>{job.is_active ? 'Active' : 'Not Active'}</td>
                                <td><button className="btn bg-blue-500 text-white" onClick={() => toggleJob(job.id)}>Toggle </button></td>
                            </tr>
                        ))}
                    </tbody>
                </table>
            </div>
        </>
    );
}
