import { Box, Center, Stack, Text, Title, rem } from "@mantine/core";

const HomePage = (): JSX.Element => {
    return (
        <Center flex={1}>
            <Stack>
                <Title fz={{ base: rem(64), sm: rem(84), md: rem(120) }} lh={1} ta="center">
                    <Text component="span" variant="gradient" inherit>
                        Hermes
                    </Text>
                </Title>
                <Text c="dimmed" ta="center">
                    A{" "}
                    <Text inherit component="span" td="underline">
                        megoldás
                    </Text>
                    , ha sportversenyek szervezéséről van szó!
                </Text>
            </Stack>
        </Center>
    );
};

export default HomePage;
