export default {
    hermes: {
        output: {
            mode: "tags-split",
            target: "./generated/api/features",
            schemas: "./generated/api/models",
            client: "react-query",
        },
        input: {
            target: "./schema/openapi.json",
        },
    },
};
