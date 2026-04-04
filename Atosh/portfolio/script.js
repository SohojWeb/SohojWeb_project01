// Load data from JSON
fetch('data.json')
    .then(response => response.json())
    .then(data => {
        // Load about text
        document.getElementById('about-text').textContent = data.about;

        // Load skills
        const skillsGrid = document.getElementById('skills-grid');
        skillsGrid.innerHTML = '';
        data.skills.forEach(skill => {
            const skillDiv = document.createElement('div');
            skillDiv.className = 'skill';
            skillDiv.textContent = skill;
            skillsGrid.appendChild(skillDiv);
        });

        // Load projects
        const projectsGrid = document.getElementById('projects-grid');
        projectsGrid.innerHTML = '';
        data.projects.forEach(project => {
            const projectDiv = document.createElement('div');
            projectDiv.className = 'project';
            projectDiv.innerHTML = `
                <img src="${project.image}" alt="${project.title}">
                <h3>${project.title}</h3>
                <p>${project.description}</p>
                <a href="${project.link}" target="_blank">View Project</a>
            `;
            projectsGrid.appendChild(projectDiv);
        });
    })
    .catch(error => console.error('Error loading data:', error));

// Contact form submission
document.getElementById('contact-form').addEventListener('submit', function(e) {
    e.preventDefault();
    alert('Thank you for your message! I will get back to you soon.');
    this.reset();
});