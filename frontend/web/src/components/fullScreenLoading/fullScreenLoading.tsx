import { Box, Center, Loader } from "@mantine/core";

import classes from "./fullScreenLoading.module.scss";

export const FullScreenLoading = (): JSX.Element => {
    return (
        <Center className={classes.container}>
            <Box className={classes.content}>
                <Loader type="dots" size="xl" mb={20} />
            </Box>
        </Center>
    );
};
