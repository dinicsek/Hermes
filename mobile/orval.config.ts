export default {
    hermes: {
        output: {
            mode: "tags-split",
            target: ".src/generated/api/features",
            schemas: ".src/generated/api/models",
            client: "react-query",
        },
        input: {
            target: "./schema/openapi.json",
        },
    },
};
