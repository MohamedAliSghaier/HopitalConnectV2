class Chatbot {
    constructor() {
        this.messages = [];
        this.isOpen = false;
        this.init();
    }

    init() {
        // Create chatbot container
        const chatbotContainer = document.createElement('div');
        chatbotContainer.className = 'chatbot-container';
        chatbotContainer.innerHTML = `
            <div class="chatbot-header">
                <h3>Assistant Médical</h3>
                <button class="minimize-btn">_</button>
            </div>
            <div class="chatbot-messages"></div>
            <div class="chatbot-input">
                <input type="text" placeholder="Écrivez votre message ici...">
                <button class="send-btn">Envoyer</button>
            </div>
        `;

        // Add to body
        document.body.appendChild(chatbotContainer);

        // Initialize event listeners
        this.initializeEventListeners(chatbotContainer);
        
        // Add welcome message
        this.addMessage('Bonjour! Je suis votre assistant médical. Comment puis-je vous aider aujourd\'hui?', 'bot');
    }

    initializeEventListeners(container) {
        const input = container.querySelector('input');
        const sendBtn = container.querySelector('.send-btn');
        const minimizeBtn = container.querySelector('.minimize-btn');
        const messagesContainer = container.querySelector('.chatbot-messages');

        sendBtn.addEventListener('click', () => this.handleSendMessage(input));
        input.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                this.handleSendMessage(input);
            }
        });

        minimizeBtn.addEventListener('click', () => {
            container.classList.toggle('minimized');
        });
    }

    handleSendMessage(input) {
        const message = input.value.trim();
        if (message) {
            this.addMessage(message, 'user');
            input.value = '';
            
            // Simulate bot response (you can replace this with actual API calls)
            setTimeout(() => {
                this.addMessage(this.getBotResponse(message), 'bot');
            }, 1000);
        }
    }

    addMessage(text, sender) {
        const messagesContainer = document.querySelector('.chatbot-messages');
        const messageDiv = document.createElement('div');
        messageDiv.className = `message ${sender}-message`;
        messageDiv.textContent = text;
        messagesContainer.appendChild(messageDiv);
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }

    getBotResponse(message) {
        const lowerMessage = message.toLowerCase();
        
        // Define response patterns
        const patterns = [
            {
                keywords: ['bonjour', 'salut', 'hello', 'hi'],
                response: 'Bonjour! Comment puis-je vous aider aujourd\'hui?'
            },
            {
                keywords: ['rendez-vous', 'rdv', 'appointment'],
                response: 'Pour prendre rendez-vous, vous pouvez cliquer sur le bouton "Prendre Rendez-vous" dans le menu ou sur la page d\'accueil.'
            },
            {
                keywords: ['médicament', 'medicament', 'ordonnance', 'prescription'],
                response: 'Vous pouvez consulter vos médicaments et ordonnances dans votre espace personnel. Pour voir la liste des médicaments disponibles, cliquez sur "Voir Médicaments" dans le menu.'
            },
            {
                keywords: ['médecin', 'docteur', 'doctor'],
                response: 'Vous pouvez trouver la liste des médecins disponibles dans la section "Médecins" du menu.'
            },
            {
                keywords: ['urgence', 'urgent', 'emergency'],
                response: 'En cas d\'urgence, veuillez appeler immédiatement le 15 ou le 112.'
            },
            {
                keywords: ['assurance', 'mutuelle', 'insurance'],
                response: 'Pour gérer vos informations d\'assurance, vous pouvez accéder à la section "Assurance" dans votre espace personnel.'
            },
            {
                keywords: ['dossier', 'medical', 'médical'],
                response: 'Votre dossier médical est accessible dans votre espace personnel. Vous pouvez y consulter vos antécédents médicaux et vos documents.'
            },
            {
                keywords: ['merci', 'thanks', 'thank you'],
                response: 'Je vous en prie! N\'hésitez pas si vous avez d\'autres questions.'
            }
        ];

        // Check for matching patterns
        for (const pattern of patterns) {
            if (pattern.keywords.some(keyword => lowerMessage.includes(keyword))) {
                return pattern.response;
            }
        }

        // Default response if no pattern matches
        return 'Je ne suis pas sûr de comprendre votre demande. Voici quelques sujets sur lesquels je peux vous aider :\n' +
               '- Prise de rendez-vous\n' +
               '- Médicaments et ordonnances\n' +
               '- Informations sur les médecins\n' +
               '- Dossier médical\n' +
               '- Assurance maladie\n' +
               'Pouvez-vous reformuler votre question en utilisant l\'un de ces termes?';
    }
}

// Initialize chatbot when the page loads
document.addEventListener('DOMContentLoaded', () => {
    new Chatbot();
}); 