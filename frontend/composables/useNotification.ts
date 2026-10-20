export const useNotification = () => {
    const notifications = useState<string[]>(`notifications`, () => []);
    const notify = (message: string) => {
        notifications.value.push(message);
    };
    const clear = () => {
        notifications.value = [];
    };
    return { notifications, notify, clear };
};
