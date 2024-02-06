import "@mantine/core/styles.css";

import { Box, MantineProvider, Title } from "@mantine/core";

import ReactDOM from "react-dom/client";

const App = () => {
    return (
        <Box>
            <Title>Hello, World!</Title>
        </Box>
    );
};

ReactDOM.createRoot(document.getElementById("root")!).render(
    <MantineProvider>
        <App />
    </MantineProvider>
);
