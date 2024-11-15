

DELETE FROM
    openai
WHERE
    slug = 'ai_webchat';

DELETE FROM
    openai_chat_category
WHERE
    slug = 'ai_webchat';